<?php
# 执行器结构模板
return array(
    # 模板1
    "user" => array(
        "query" => "update [:table:] set user_name eq [:user_name] where id eq [:id]",
        "column" => array(
            array(
                "table" => "user",
            ),
            "user" => array(
                "table" => "user",
                "field" => array("username","set_time"),
                "where" => "id neq 0",
                "order" => array("id" => "desc"),
                "group" => array("id"),
                "limit" => array("begin" => 0,"length" => 10),
            ),
            "peo" => array(
                "table" => "peo",
                "field" => array("user_name","name_age" => "age"),
                "where" => "id neq 0",
                "order" => array("id" => "desc"),
                "group" => array("id"),
                "limit" => array("begin" => 0,"length" => 10),
            ),
        ),
    ),

    # 模板2
    "user1" => array(
        "table_name" => "user",
        "cycle_time" => 0,
//    "major_key" => array("column" => "id", "field" => "id", "type" => "int", "size" => 0 "is_null"=> false),
//    "major_key" => array("column" => "id", "field" => "id", "type" => "int", "is_null"=> false),
        "major_key" => array("column" => "id", "field" => "id", "type" => "int", "is_null"=> false, "auto_increment"=> true),
        "column_list" => array(
            array("column" => "user_name", "field" => "name", "type" => "string", "size" => 8, "not_null"=> false,'query' => 'insert'),
            array("column" => "user_age", "field" => "age", "type" => "int", "size" => 3, "not_null"=> true, "default"=>0,'query' => 'insert'),
            array("column" => "user_sex", "field" => "sex", "type" => "int", "size" => 2, "not_null"=> false, "default"=>0,'query' => 'insert'),
            array("column" => "user_address", "field" => "address", "type" => "string", "size" => 255, "not_null"=> true,'query' => 'insert'),
        ),
    ),
);