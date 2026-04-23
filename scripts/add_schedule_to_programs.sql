-- Add schedule fields to programs table
ALTER TABLE programs 
ADD COLUMN IF NOT EXISTS start_date DATE,
ADD COLUMN IF NOT EXISTS end_date DATE,
ADD COLUMN IF NOT EXISTS schedule VARCHAR(255);

-- Add comments for clarity
COMMENT ON COLUMN programs.start_date IS 'Fecha de inicio del programa';
COMMENT ON COLUMN programs.end_date IS 'Fecha de fin del programa';
COMMENT ON COLUMN programs.schedule IS 'Horario del programa (ej: Lunes a Viernes 18:00 - 21:00)';
