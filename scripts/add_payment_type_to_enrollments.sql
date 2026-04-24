-- Agregar campo payment_type a enrollments
ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS payment_type VARCHAR(20) DEFAULT 'contado';
-- Valores: 'contado' (pago unico) o 'cuotas' (multiples pagos)

-- Agregar campo num_installments (numero de cuotas) si es en cuotas
ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS num_installments INTEGER DEFAULT 1;

-- Agregar campo payment_proof a payments para guardar la foto del comprobante
ALTER TABLE payments ADD COLUMN IF NOT EXISTS payment_proof VARCHAR(255);
