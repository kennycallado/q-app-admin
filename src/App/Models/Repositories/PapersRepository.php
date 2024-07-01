<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Paper;
use Core\Utils\SurrealDB;

class PapersRepository
{
    private SurrealDB $db;

    public static array $queries = [
        'all' => 'SELECT * FROM papers ORDER BY created DESC;',
        'findByUserId' => 'SELECT id, user, answers, completed, resource, created FROM papers WHERE {criteria} = {id} ORDER BY created DESC;'
    ];

    public function __construct(SurrealDB $db)
    {
        $this->db = $db;
    }

    /**
     * @throws \Exception
     * @return Paper[]
     */
    public function all(): array
    {
        $papers = [];

        $db_res = $this->db->rawQuery(self::$queries['all'])[0]->result;
        foreach ($db_res as $row) {
            $row = (array) $row;
            $papers[] = new Paper(...$row);
        }

        return $papers;
    }

    /**
     * @return Paper[]
     */
    public function finByUserId(string $criteria, string $id): array
    {
        $papers = [];
        $sql = str_replace('{criteria}', $criteria, self::$queries['findByUserId']);
        $sql = str_replace('{id}', $id, $sql);

        $db_res = $this->db->rawQuery($sql)[0]->result;
        foreach ($db_res as $row) {
            $row = (array) $row;
            $papers[] = new Paper(...$row);
        }

        return $papers;
    }
}
