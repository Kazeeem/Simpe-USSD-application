<?php


namespace App;


class Agent
{
    protected $agent_no;

    public function __construct($agent_no)
    {
        $this->agent_no = $agent_no;
    }

    public function getAgentId($pdo)
    {
        $stmt = $pdo->prepare("SELECT id FROM agents WHERE agent_no=?");
        $stmt->execute([$this->agent_no]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        return $row['id'];
    }

    public function getAgentName($pdo): string
    {
        $stmt = $pdo->prepare("SELECT `name` FROM agents WHERE agent_no=?");
        $stmt->execute([$this->agent_no]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        return $row['name'];
    }
}