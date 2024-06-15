<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Paragraph;
use Core\Utils\SurrealDB;

class ParagraphsRepository
{
    private SurrealDB $db;

    public static array $queries = [ ];

    public function __construct(SurrealDB $db)
    {
        $this->db = $db;
    }

    public function all()
    {
        $db_res = $this->db->select('*')->tables('paragraphs')->exec();

        if ($db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching paragraphs');
        }

        return $db_res[0]->result;
    }

    public function find(string $id)
    {
        $db_res = $this->db->select('*')->tables('paragraphs')->where("id = $id")->exec();

        if ($db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching paragraph');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Paragraph $content
     * @throws \Exception
     * @return Paragraph
     */
    public function create(Paragraph $content)
    {
        $db_res = $this->db->create('paragraphs')->data($content)->exec();

        if ($db_res[0]->status !== 'OK') {
            throw new \Exception('Error creating paragraph');
        }

        $content->id = $db_res[0]->result[0]->id;
        return $content;
    }
}
