<?php

function validateTransaction(array $input): array
{
    $errors = [];

    // validate type
    if (!isset($input['type'])) {
        $errors['type'] = 'Tipe transaksi harus diisi.';
    } elseif (!in_array($input['type'], ['income', 'expense'], true)) {
        $errors['type'] = 'Tipe transaksi tidak valid.';
    }

    // validate amount
    if (!isset($input['amount'])) {
        $errors['amount'] = 'Jumlah transaksi harus diisi.';
    } elseif(!is_int($input['amount']) || $input['amount'] <= 0) {
        $errors['amount'] = 'Jumlah transaksi harus berupa angka positif.';
    }

    // validate transaction_date
    if (!isset($input['transaction_date'])) {
    $errors['transaction_date'] = 'Tanggal wajib diisi';
    } else {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', (string) $input['transaction_date']);

        if (!$date || $date->format('Y-m-d') !== $input['transaction_date']) {
            $errors['transaction_date'] = 'Tanggal harus valid dengan format YYYY-MM-DD';
        }
    }

    return $errors;
}