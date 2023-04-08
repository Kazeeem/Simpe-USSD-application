<?php


namespace App;


class Transaction
{
    protected $amount;

    public function __construct($amount)
    {
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getType()
    {
        return $this->type;
    }

    public function sendMoney($pdo, $sender_id, $receiver_id, $sender_balance, $receiver_balance)
    {
        $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, FALSE);

        try {
            $pdo->beginTransaction();

            $transaction_stmt = $pdo->prepare("INSERT INTO transactions (amount, user_id, type, receiver_id) VALUES (?,?,?,?)");
            $balance_stmt = $pdo->prepare("UPDATE users SET balance=? WHERE user_id=?");

            $transaction_stmt->execute([$this->getAmount(), $receiver_id, Utility::CREDIT_TRANSACTION, $sender_id]);
            $transaction_stmt->execute([$this->getAmount(), $sender_id, Utility::DEBIT_TRANSACTION, $receiver_id]);
            $balance_stmt->execute([$sender_balance, $sender_id]);
            $balance_stmt->execute([$receiver_balance, $receiver_id]);

            $pdo->commit();
            return true;
        }
        catch (\Exception $e) {
            $pdo->rollback();
            return "END Transaction was unsuccessful";
        }
    }

    public function withdrawCash($pdo, $user_id, $agent_id, $new_balance)
    {

    }
}