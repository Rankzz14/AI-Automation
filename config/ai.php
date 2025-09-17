<?php
return [
  'guest_credits' => 10,          // guest başlangıç kredisi
  'guest_expires_days' => 7,      // guest krediler kaç günde sıfırlanır
  'max_tokens_per_run' => 200,    // her run için rezervasyon sınırı
  'default_model' => 'gpt-4o-mini',
  'model_price_per_1000_tokens_cents' => 1, // placeholder (fiyatı prod'ta güncelle)
];