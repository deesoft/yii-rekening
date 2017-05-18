<?php

use yii\db\Migration;

class m170515_042913_init_rekening extends Migration
{
    public function up()
    {
        $this->createTable('{{%address}}', [
            'id' => $this->primaryKey(),
            'code'=> $this->string(20),
            'address' => $this->string()
        ]);

        $this->createTable('{{%customer}}',[
            'id' => $this->primaryKey(),
            'code'=> $this->string(20)->notNull(),
            'name'=> $this->string(128),
            'address_id' => $this->integer(),
            'status'=> $this->integer()->defaultValue(1),

            'FOREIGN KEY ([[address_id]]) REFERENCES {{%address}} ([[id]]) ON DELETE RESTRICT ON UPDATE CASCADE'
        ]);
        
        $this->createTable('{{%price}}', [
            'id' => $this->primaryKey(),
            'group' => $this->string(16),
            'date_from' => $this->date()->notNull(),
            'ammount_min' => $this->double(),
            'meter_min' => $this->integer(),
            'price' => $this->double(),
            'threshold_1' => $this->integer(),
            'price_1' => $this->double(),
            'threshold_2' => $this->integer(),
            'price_2' => $this->double(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%price}}');
        $this->dropTable('{{%customer}}');
        $this->dropTable('{{%address}}');
    }
}
