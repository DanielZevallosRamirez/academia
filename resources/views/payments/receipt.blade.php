<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - {{ $payment->receipt_number ?? $payment->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo h1 {
            font-size: 28px;
            color: #1f2937;
        }
        .logo p {
            color: #6b7280;
            font-size: 14px;
        }
        .receipt-info {
            text-align: right;
        }
        .receipt-info h2 {
            font-size: 24px;
            color: #10b981;
            margin-bottom: 5px;
        }
        .receipt-info p {
            color: #6b7280;
            font-size: 14px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .detail-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .detail-section p {
            color: #1f2937;
            font-size: 14px;
            line-height: 1.6;
        }
        .detail-section p strong {
            display: block;
            font-size: 16px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .payment-table th {
            background: #f9fafb;
            padding: 12px 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }
        .payment-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
        }
        .payment-table .amount {
            text-align: right;
            font-weight: 600;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .totals-box {
            width: 300px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals-row.total {
            border-bottom: none;
            font-size: 18px;
            font-weight: 700;
            color: #10b981;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-pagado {
            background: #d1fae5;
            color: #065f46;
        }
        .status-parcial {
            background: #fef3c7;
            color: #92400e;
        }
        .status-pendiente {
            background: #dbeafe;
            color: #1e40af;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin: 40px 0;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #1f2937;
            padding-top: 10px;
            margin-top: 60px;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .print-btn:hover {
            background: #059669;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt {
                box-shadow: none;
                padding: 20px;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">Imprimir Recibo</button>

    <div class="receipt">
        <div class="header">
            <div class="logo">
                <h1>{{ config('app.name', 'Academia') }}</h1>
                <p>Sistema de Gestion Academica</p>
            </div>
            <div class="receipt-info">
                <h2>RECIBO DE PAGO</h2>
                <p><strong>N: {{ $payment->receipt_number ?? str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p>Fecha: {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="details-grid">
            <div class="detail-section">
                <h3>Datos del Estudiante</h3>
                <p>
                    <strong>{{ $payment->enrollment->student->name }} {{ $payment->enrollment->student->last_name }}</strong>
                    DNI: {{ $payment->enrollment->student->dni ?? 'No registrado' }}<br>
                    Email: {{ $payment->enrollment->student->email }}<br>
                    Telefono: {{ $payment->enrollment->student->phone ?? 'No registrado' }}
                </p>
            </div>
            <div class="detail-section">
                <h3>Programa / Inscripcion</h3>
                <p>
                    <strong>{{ $payment->enrollment->program->name }}</strong>
                    Fecha de inscripcion: {{ $payment->enrollment->start_date->format('d/m/Y') }}<br>
                    Estado: {{ ucfirst($payment->enrollment->status) }}
                </p>
            </div>
        </div>

        <table class="payment-table">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Descripcion</th>
                    <th class="amount">Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ ucfirst($payment->concept) }}
                        @if($payment->installment_number)
                            <br><small style="color: #6b7280;">Cuota {{ $payment->installment_number }}</small>
                        @endif
                    </td>
                    <td>{{ $payment->notes ?? 'Pago correspondiente al programa' }}</td>
                    <td class="amount">S/ {{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="totals-row">
                    <span>Subtotal:</span>
                    <span>S/ {{ number_format($payment->amount, 2) }}</span>
                </div>
                @if($payment->amount_paid && $payment->amount_paid < $payment->amount)
                <div class="totals-row">
                    <span>Monto pagado:</span>
                    <span>S/ {{ number_format($payment->amount_paid, 2) }}</span>
                </div>
                <div class="totals-row">
                    <span>Saldo pendiente:</span>
                    <span>S/ {{ number_format($payment->amount - $payment->amount_paid, 2) }}</span>
                </div>
                @endif
                <div class="totals-row total">
                    <span>TOTAL PAGADO:</span>
                    <span>S/ {{ number_format($payment->amount_paid ?? $payment->amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Metodo de pago:</strong> {{ ucfirst($payment->payment_method ?? 'No especificado') }}
            <br>
            <strong>Estado:</strong> 
            <span class="status-badge status-{{ $payment->status }}">
                {{ ucfirst($payment->status) }}
            </span>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    Firma del Estudiante
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Sello y Firma de la Academia
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Este documento es un comprobante de pago valido.</p>
            <p>Generado el {{ now()->format('d/m/Y H:i') }} | {{ config('app.name', 'Academia') }}</p>
        </div>
    </div>
</body>
</html>
