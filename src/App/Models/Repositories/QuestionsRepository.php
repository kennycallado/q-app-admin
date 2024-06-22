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
     * QuestionsRepository constructor.
     * @param SurrealDB $db
     */
    public function __construct(SurrealDB $db)
    {
        $this->db = $db;
    }

    /**
     * @return Question[]
     * @throws \Exception
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
     * @return Question
     * @throws \Exception
     */
    public function find(string $id): object
    {
        $db_res = $this->db->select('*')->tables('questions')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching question');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Question $content
     * @return Question
     * @throws \Exception
     */
    public function create(Question $content): object
    {
        $db_res = $this->db->create('questions')->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error creating question');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Question $content
     * @return Question
     * @throws \Exception
     */
    public function update(Question $content): object
    {
        $db_res = $this->db->update($content->id)->merge($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating question');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param string $id
     * @return bool
     * @throws \Exception
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
