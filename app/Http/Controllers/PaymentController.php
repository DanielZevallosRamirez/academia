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

        $stats = [
            'total_pendiente' => Payment::pending()->sum('amount'),
            'total_pagado_mes' => Payment::paid()->thisMonth()->sum('amount'),
            'cantidad_vencidos' => Payment::overdue()->count(),
            'cantidad_pendientes' => Payment::pending()->count(),
        ];

        return view('payments.index', compact('payments', 'stats'));
    }

    public function create(Request $request)
    {
        $students = User::estudiantes()->active()->with('enrollments.program')->get();
        $selectedStudent = $request->filled('student_id') 
            ? User::find($request->student_id) 
            : null;

        return view('payments.create', compact('students', 'selectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'payment_method' => 'nullable|in:efectivo,transferencia,tarjeta,online',
            'notes' => 'nullable|string',
        ]);

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
            'due_date' => 'required|date',
            'status' => 'required|in:pendiente,pagado,vencido,cancelado',
            'payment_method' => 'nullable|in:efectivo,transferencia,tarjeta,online',
            'paid_date' => 'nullable|date',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

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
            'payment_method' => 'required|in:efectivo,transferencia,tarjeta,online',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'status' => 'pagado',
            'paid_date' => today(),
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'],
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Pago marcado como pagado.');
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
}
