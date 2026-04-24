<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentDocument;
use App\Models\PaymentPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'enrollment.program']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(15);

        // Calculate stats - simpler approach
        $allPayments = Payment::all();
        
        $totalPendiente = $allPayments->whereIn('status', ['pendiente', 'parcial', 'vencido'])
            ->sum(function($p) {
                return $p->amount - ($p->amount_paid ?? 0);
            });
        
        $totalPagadoMes = $allPayments
            ->filter(function($p) {
                return $p->created_at->month == now()->month 
                    && $p->created_at->year == now()->year
                    && in_array($p->status, ['pagado', 'parcial']);
            })
            ->sum('amount_paid');
        
        $cantidadVencidos = $allPayments
            ->where('status', 'pendiente')
            ->filter(function($p) {
                return $p->due_date && $p->due_date < today();
            })
            ->count();
        
        $cantidadPendientes = $allPayments->whereIn('status', ['pendiente', 'parcial'])->count();
        
        $stats = [
            'total_pendiente' => $totalPendiente,
            'total_pagado_mes' => $totalPagadoMes,
            'cantidad_vencidos' => $cantidadVencidos,
            'cantidad_pendientes' => $cantidadPendientes,
        ];

        return view('payments.index', compact('payments', 'stats'));
    }

    public function create(Request $request)
    {
        $students = User::estudiantes()->active()->with('enrollments.program')->get();
        $enrollments = Enrollment::with(['student', 'program'])
            ->whereHas('student', function($q) {
                $q->where('status', 'activo');
            })
            ->get();
        $selectedStudent = $request->filled('student_id') 
            ? User::find($request->student_id) 
            : null;

        return view('payments.create', compact('students', 'selectedStudent', 'enrollments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'payment_method' => 'nullable|in:efectivo,transferencia,tarjeta,online,yape',
            'concept' => 'nullable|string',
            'installment_number' => 'nullable|integer|min:1',
            'amount_paid' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pendiente,pagado,parcial',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Get user_id from enrollment
        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);
        $validated['user_id'] = $enrollment->user_id;

        // Handle file upload
        if ($request->hasFile('receipt_file')) {
            $path = $request->file('receipt_file')->store('payment-proofs', 'public');
            $validated['payment_proof'] = $path;
        }

        // Determine status based on amount paid
        $amountPaid = $validated['amount_paid'] ?? 0;
        if ($amountPaid >= $validated['amount']) {
            $validated['status'] = 'pagado';
            $validated['paid_date'] = now();
        } elseif ($amountPaid > 0) {
            $validated['status'] = 'parcial';
        } else {
            $validated['status'] = $validated['status'] ?? 'pendiente';
        }

        $payment = Payment::create($validated);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Pago registrado exitosamente.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['student', 'enrollment.program', 'documents']);

        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'amount_paid' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:pendiente,parcial,pagado,vencido,cancelado',
            'payment_method' => 'nullable|in:efectivo,transferencia,tarjeta,online,yape,plin',
            'paid_date' => 'nullable|date',
            'transaction_id' => 'nullable|string',
            'concept' => 'nullable|string',
            'installment_number' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'payment_proof' => 'nullable|image|max:5120',
        ]);

        // Handle payment proof upload
        if ($request->hasFile('payment_proof')) {
            // Delete old proof if exists
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            $validated['payment_proof'] = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        $payment->update($validated);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Pago actualizado exitosamente.');
    }

    public function destroy(Payment $payment)
    {
        // Eliminar documentos asociados
        foreach ($payment->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
        }

        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Pago eliminado exitosamente.');
    }

    public function markAsPaid(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:efectivo,transferencia,tarjeta,online,yape',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_proof' => 'nullable|image|max:5120',
        ]);

        $updateData = [
            'status' => 'pagado',
            'paid_date' => today(),
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'],
            'notes' => $validated['notes'],
        ];

        // Handle payment proof upload
        if ($request->hasFile('payment_proof')) {
            // Delete old proof if exists
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $updateData['payment_proof'] = $path;
        }

        $payment->update($updateData);

        return back()->with('success', 'Pago marcado como pagado.');
    }

    public function uploadProof(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:5120',
        ]);

        // Delete old proof if exists
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');
        $payment->update(['payment_proof' => $path]);

        return back()->with('success', 'Comprobante subido exitosamente.');
    }

    public function uploadDocument(Request $request, Payment $payment)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $file = $request->file('document');
        $path = $file->store('payment-documents', 'public');

        PaymentDocument::create([
            'payment_id' => $payment->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Documento subido exitosamente.');
    }

    public function deleteDocument(PaymentDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Documento eliminado exitosamente.');
    }

    public function createPaymentPlan(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'total_installments' => 'required|integer|min:1|max:24',
            'day_of_month' => 'required|integer|min:1|max:28',
        ]);

        $installmentAmount = $enrollment->program->price / $validated['total_installments'];

        $plan = PaymentPlan::create([
            'enrollment_id' => $enrollment->id,
            'total_installments' => $validated['total_installments'],
            'installment_amount' => $installmentAmount,
            'day_of_month' => $validated['day_of_month'],
        ]);

        $plan->generatePayments();

        return back()->with('success', 'Plan de pagos creado exitosamente.');
    }

    public function report(Request $request)
    {
        $query = Payment::with(['student', 'enrollment.program']);

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $payments = $query->get();

        $summary = [
            'total_facturado' => $payments->sum('amount'),
            'total_cobrado' => $payments->where('status', 'pagado')->sum('amount'),
            'total_pendiente' => $payments->where('status', 'pendiente')->sum('amount'),
            'total_vencido' => $payments->filter(fn($p) => $p->isOverdue())->sum('amount'),
            'por_metodo' => $payments->where('status', 'pagado')->groupBy('payment_method')
                ->map(fn($group) => $group->sum('amount')),
        ];

        return view('payments.report', compact('payments', 'summary'));
    }

    public function overdue()
    {
        $payments = Payment::with(['student', 'enrollment.program'])
            ->overdue()
            ->orderBy('due_date')
            ->paginate(15);

        return view('payments.overdue', compact('payments'));
    }

    public function pending()
    {
        $payments = Payment::with(['student', 'enrollment.program'])
            ->pending()
            ->orderBy('due_date')
            ->paginate(15);

        return view('payments.pending', compact('payments'));
    }

    public function process(Payment $payment)
    {
        $payment->load(['student', 'enrollment.program']);

        return view('payments.process', compact('payment'));
    }

    public function processPayment(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'payment_method' => 'required|in:efectivo,transferencia,tarjeta,yape',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $amountPaid = $validated['amount_paid'];
        $totalPaid = ($payment->amount_paid ?? 0) + $amountPaid;

        $payment->update([
            'amount_paid' => $totalPaid,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'],
            'notes' => $validated['notes'],
            'status' => $totalPaid >= $payment->amount ? 'pagado' : 'parcial',
            'paid_at' => $totalPaid >= $payment->amount ? now() : null,
        ]);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Pago procesado exitosamente.');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['student', 'enrollment.program']);

        return view('payments.receipt', compact('payment'));
    }
}
