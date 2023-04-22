<?php namespace App\Http\Traits;

use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;

trait CasesTrait
{
    public static function getCases($data)
    {
        try {
            return self::buildQuery($data);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public static function getCase($data)
    {
        try {
            $case = decrypt($data);
            $query = "
SELECT
c.id,
p.name,
p.\"surName\",
p.\"secondSurName\",
p.alias,
p.\"birthDate\",
p.\"birthState\",
p.\"birthLocality\",
p.sre_cat_person_marital_status_id,
p.sre_cat_person_gender_id,
p.ethnic_group_id,
pc.email

FROM
spc_case_single_registrations c
LEFT JOIN spc_case_single_people_addeds AS pa
ON c.id = pa.case_single_registration_id
AND pa.case_person_type_id = 1
LEFT JOIN spc_people p ON p.id = pa.person_id
LEFT JOIN geo_cat_states ste ON ste.id = p.\"birthState\"
LEFT JOIN spc_people_contact_emails pc ON pc.person_id = p.id
WHERE c.id = $case

            ";
            return DB::select($query);


        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public static function buildQuery($filters)
    {
        try {
            $perPage = $filters['pagination']['rowsPerPage'];
            $page = $filters['pagination']['page'];
            $query = "SELECT
                    c.id,
                    c.\"classificationSre\",
                    INITCAP(o.\"shortName\") as office_name,
                    INITCAP(c.\"description\") as description,
                    INITCAP(s.\"name\") as status_name,
                    SUM(r.amount) as total_amount,
                    EXTRACT(YEAR from AGE(NOW(), pe.\"birthDate\")) as age,
                    to_char(pe.\"birthDate\", 'DD-MM-YYYY') as \"birthDate\",
                    CONCAT(INITCAP(pe.\"name\"), ' ', INITCAP(pe.\"surName\"), ' ', INITCAP(pe.\"secondSurName\")) as full_name,
                    pe.\"birthDate\" as titular_birth_date,
                    CONCAT(INITCAP(pemp.name), ' ', INITCAP(pemp.\"firstName\"), ' ', INITCAP(pemp.\"secondName\")) as user_full_name,
                    us.username as username,
                    c.created_at
                FROM spc_case_single_registrations c
                LEFT JOIN sre_cat_offices o ON o.id = c.sre_cat_office_id
                LEFT JOIN spc_cat_registration_statuses s ON s.id = c.status_id
                LEFT JOIN spc_case_single_people_addeds pa ON pa.case_single_registration_id = c.id
                LEFT JOIN spc_people pe ON pe.id = pa.person_id
                LEFT JOIN spc_case_single_resources r ON r.case_single_registration_id = c.id
                LEFT JOIN sre_users us ON us.id = c.created_by
                LEFT JOIN sre_employees emp ON emp.id = us.sre_employee_id
                LEFT JOIN sre_people_employed pemp ON pemp.id = emp.sre_person_employed_id
                WHERE c.\"isActive\" is true
                AND c.deleted_at is null
                AND c.sre_cat_office_id is not null
                AND c.\"classificationSre\" is not null
";

            if (count($filters) > 0) {
                $query .= self::setFilters($filters);
            }

            $query .= "GROUP BY c.id,
                o.\"shortName\",
                pe.name,
                age,
                pe.\"secondSurName\",
                pe.\"surName\",
                pe.\"birthDate\",
                pemp.name,
                pemp.\"firstName\",
                pemp.\"secondName\",
                s.\"name\",
                us.username,
                c.created_at
                ORDER BY c.sre_cat_office_id asc, c.\"classificationSre\" asc, c.created_at desc
                OFFSET (($page - 1) * $perPage) FETCH NEXT $perPage ROWS ONLY
                ";
            $cases = DB::select($query);
            array_walk($cases, function(&$case) {
                $case->id = encrypt($case->id);
            });
            $total = "
            SELECT COUNT(*) as total
FROM spc_case_single_registrations c
LEFT JOIN sre_cat_offices o ON o.id = c.sre_cat_office_id
LEFT JOIN spc_case_single_people_addeds pa ON pa.case_single_registration_id = c.id
LEFT JOIN spc_people pe ON pe.id = pa.person_id
LEFT JOIN spc_case_single_resources r ON r.case_single_registration_id = c.id
LEFT JOIN sre_users us ON us.id = c.created_by
LEFT JOIN sre_employees emp ON emp.id = us.sre_employee_id
LEFT JOIN sre_people_employed pemp ON pemp.id = emp.sre_person_employed_id
WHERE c.\"isActive\" is true
  AND c.deleted_at is null
  AND c.sre_cat_office_id is not null

            ";
            if (count($filters) > 0) {
                $total .= self::setFilters($filters);
            }
            $total = DB::select($total);
            return [
                'total' => $total[0]->total,
                'cases' => $cases
            ];
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,

                'message' => $exception->getMessage()
            ]);
        }
    }

    public static function setFilters($filters)
    {
        try {
            $whereClause = "";
            if (isset($filters['name'])) {
                $whereClause .= " AND unaccent(pe.\"name\") ILIKE unaccent('%" . $filters['name'] . "%')";
            }
            if (isset($filters['alias'])) {
                $whereClause .= " AND unaccent(pe.\"alias\") ILIKE unaccent('%" . $filters['alias'] . "%')";
            }
            if (isset($filters['surName'])) {
                $whereClause .= " AND unaccent(pe.\"surName\") ILIKE unaccent('%" . $filters['surName'] . "%')";
            }
            if (isset($filters['secondSurName'])) {
                $whereClause .= " AND unaccent(pe.\"secondSurName\") ILIKE unaccent('%" . $filters['secondSurName'] . "%')";
            }
            if (isset($filters['classificationSre'])) {
                $whereClause .= " AND unaccent(c.\"classificationSre\") ILIKE unaccent('%" . $filters['classificationSre'] . "%')";
            }
            if (isset($filters['spc_cat_environment_id'])) {
                $whereClause .= " AND c.spc_cat_environment_id = " . $filters['spc_cat_environment_id'];
            }
            if (isset($filters['spc_cat_category_id'])) {
                $whereClause .= " AND c.spc_cat_category_id = " . $filters['spc_cat_category_id'];
            }
            if (isset($filters['sre_cat_office_id'])) {
                $ids = implode(",", $filters['sre_cat_office_id']);
                $whereClause .= " AND c.sre_cat_office_id in ($ids)";
            }
            if (isset($filters['status_id'])) {
                $ids = implode(",", $filters['status_id']);
                $whereClause .= " AND c.status_id in ($ids) ";
            }
            if (isset($filters['hate_crime_victim_id']) && $filters['hate_crime_victim_id'] === true) {
                $whereClause .= " AND c.hate_crime_victim_id >= 1  ";
            }
            if (isset($filters['createFrom']) && isset($filters['createTo'])) {
                $createFrom = Carbon::parse($filters['createFrom'])->format('Y-m-d');
                $createTo = Carbon::parse($filters['createTo'])->format('Y-m-d');
                $whereClause .= " AND c.created_at BETWEEN '$createFrom 00:00:00' AND '$createTo 23:59:59'";
            }

            return $whereClause;

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

}
