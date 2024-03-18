<?php
class M_production extends Model
{
    function getproductionplan($where = "")
    {
        $this->dbts = $this->load->database("webtracking_ts", true);
        $this->dbts->select("*");
        if (isset($where['date'])) {
            $this->dbts->where("plan_date", $where['date']);
        }
        $result = $this->dbts->get("ts_prod_plan");
        return $result->result_array();
    }

    function insertproductionplan($data)
    {
        $this->dbts = $this->load->database("webtracking_ts", true);
        return $this->dbts->insert("ts_prod_plan", $data);
    }

    function updateproductionplan($data, $id)
    {
        $this->dbts = $this->load->database("webtracking_ts", true);
        $this->dbts->where("plan_id", $id);
        return $this->dbts->update("ts_prod_plan", $data);
    }
}
