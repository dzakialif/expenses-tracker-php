CREATE TABLE IF NOT EXISTS transactions (
    id               BIGSERIAL PRIMARY KEY,
    type             VARCHAR(10) NOT NULL CHECK (type IN ('income', 'expense')),
    amount           BIGINT NOT NULL CHECK (amount > 0),
    transaction_date DATE NOT NULL,
    note             TEXT,
    created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);