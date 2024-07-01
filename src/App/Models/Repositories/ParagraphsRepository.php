<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Paragraph;
use Core\Utils\SurrealDB;

/**
 * Class ParagraphsRepository
 * @package App\Models\Repositories
 */
class ParagraphsRepository
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
     * @return Paragraph[]
     */
    public function all(): array
    {
        $db_res = $this->db->select('*')->tables('paragraphs')->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching paragraphs');
        }

        return $db_res[0]->result;
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return Paragraph
     */
    public function find(string $id): object
    {
        $db_res = $this->db->select('*')->tables('paragraphs')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching paragraph');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Paragraph $content
     * @throws \Exception
     * @return Paragraph
     */
    public function create(Paragraph $content): object
    {
        unset($content->id);
        $db_res = $this->db->create('paragraphs')->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error creating paragraph');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Paragraph $content
     * @throws \Exception
     * @return Paragraph
     */
    public function update(Paragraph $content): object
    {
        $db_res = $this->db->update($content->id)->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating paragraph');
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
        $db_res = $this->db->delete('paragraphs')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error deleting paragraph');
        }

        return true;
    }
}
