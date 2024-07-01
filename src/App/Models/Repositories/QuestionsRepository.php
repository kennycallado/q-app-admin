<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Question;
use Core\Utils\SurrealDB;

/**
 * Class QuestionsRepository
 * @package App\Models\Repositories
 */
class QuestionsRepository
{
    private SurrealDB $db;
    public static array $queries = [];

    /**
     * @param SurrealDB $db
     */
    public function __construct(SurrealDB $db)
    {
        $this->db = $db;
    }

    /**
     * @throws \Exception
     * @return Question[]
     */
    public function all(): array
    {
        $db_res = $this->db->select('*')->tables('questions')->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching questions');
        }

        return $db_res[0]->result;
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return Question
     */
    public function find(string $id): object
    {
        $db_res = $this->db->select('*')->tables('questions')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching question');
        }

        /** @var User $user */
        return $db_res[0]->result[0];
    }

    /**
     * @param Question $content
     * @throws \Exception
     * @return Question
     */
    public function create(Question $content): object
    {
        unset($content->id);
        $db_res = $this->db->create('questions')->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error creating question');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Question $content
     * @throws \Exception
     * @return Question
     */
    public function update(Question $content): object
    {
        $db_res = $this->db->update($content->id)->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating question');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param string $id
     * @param object $content
     * @throws \Exception
     * @return Question
     */
    public function patch(string $id, object $content): object
    {
        $db_res = $this->db->update($id)->merge($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating question');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function delete(string $id): bool
    {
        $db_res = $this->db->delete($id)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error deleting question');
        }

        return true;
    }
}
