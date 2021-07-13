<?php

namespace App\Http\Helpers;

class StripeHelper
{
    public static function createCustomer($data)
    {
        $stripe = new \Stripe\StripeClient(env("STRIPE_SECRET_KEY"));
        return $stripe->customers->create($data);
    }

    public static function createEmphemeralKey($cust_id)
    {
        \Stripe\Stripe::setApiKey(env("STRIPE_SECRET_KEY"));
        $key = \Stripe\EphemeralKey::create(
            ['customer' => $cust_id],
            ['stripe_version' => env('STRIPE_API_VERSION')]
        );
        return $key;
    }

    public static function createPaymentIntent($data)
    {
        \Stripe\Stripe::setApiKey(env("STRIPE_SECRET_KEY"));
        $intent = \Stripe\PaymentIntent::create([
            'amount' => (float) $data['amount'],
            'currency' => $data['currency'],
            'customer' => $data['customer']
        ]);
        return $intent->client_secret;
    }

    public static function fetchCards($user_id)
    {
        $stripe = new \Stripe\StripeClient(
            env("STRIPE_SECRET_KEY")
        );

        $response = $stripe->customers->allSources(
            $user_id,
            ['object' => 'card', 'limit' => 10]
        );
        return $response['data'];
    }

    public static function deleteCard($user_id, $card_id)
    {
        $stripe = new \Stripe\StripeClient(
            env("STRIPE_SECRET_KEY")
        );

        $stripe->customers->deleteSource(
            $user_id,
            $card_id,
            []
        );
        return true;
    }

    public static function createCard($user_id, $amex_token)
    {
        $stripe = new \Stripe\StripeClient(
            env("STRIPE_SECRET_KEY")
        );
        $card = $stripe->customers->createSource(
            $user_id,
            ['source' => $amex_token]
        );
        return $card;
    }
}
