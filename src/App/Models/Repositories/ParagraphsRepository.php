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
     * ParagraphsRepository constructor.
     * @param SurrealDB $db
     */
    public function __construct(SurrealDB $db)
    {
        $this->db = $db;
    }

    /**
     * @return Paragraph[]
     * @throws \Exception
     */
    public function all()
    {
        $db_res = $this->db->select('*')->tables('paragraphs')->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching paragraphs');
        }

        return $db_res[0]->result;
    }

    /**
     * @param string $id
     * @return Paragraph
     * @throws \Exception
     */
    public function find(string $id)
    {
        $db_res = $this->db->select('*')->tables('paragraphs')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching paragraph');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Paragraph $content
     * @return Paragraph
     * @throws \Exception
     */
    public function create(Paragraph $content)
    {
        $db_res = $this->db->create('paragraphs')->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error creating paragraph');
        }

        $content->id = $db_res[0]->result[0]->id;
        return $content;
    }

    /**
     * @param Paragraph $content
     * @return Paragraph
     * @throws \Exception
     */
    public function update(Paragraph $content)
    {
        $db_res = $this->db->update($content->id)->merge($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating paragraph');
        }

        return $content;
    }

    /**
     * @param string $id
     * @return bool
     * @throws \Exception
     */
    public function delete(string $id)
    {
        $db_res = $this->db->delete('paragraphs')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error deleting paragraph');
        }

        return true;
    }
}
