<?php

use yii\db\Migration;

class m170515_050522_transaction extends Migration
{
    public function up()
    {
        $this->createTable('{{%periode}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32),
            'date_from' => $this->date(),
            'date_to' => $this->date(),
            'status' => $this->integer(),
        ]);

        $this->createTable('{{%data_meter}}', [
            'id' => $this->primaryKey(),
            'periode_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'meter' => $this->integer()->notNull(),

            'FOREIGN KEY ([[periode_id]]) REFERENCES {{%periode}} ([[id]]) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY ([[customer_id]]) REFERENCES {{%customer}} ([[id]]) ON DELETE RESTRICT ON UPDATE CASCADE',
        ]);

        $this->createTable('{{%invoice}}', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull(),
            'periode_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'type' => $this->string(16),
            'note' => $this->string(),
            'price_id' => $this->integer(),
            'ammount' => $this->double()->notNull(),
            'ammount_component' => $this->text(),

            'FOREIGN KEY ([[periode_id]]) REFERENCES {{%periode}} ([[id]]) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY ([[customer_id]]) REFERENCES {{%customer}} ([[id]]) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY ([[price_id]]) REFERENCES {{%price}} ([[id]]) ON DELETE RESTRICT ON UPDATE CASCADE',
        ]);

        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'ammount' => $this->double()->notNull(),
            'user_id' => $this->integer()->notNull(),
            
            'FOREIGN KEY ([[customer_id]]) REFERENCES {{%customer}} ([[id]]) ON DELETE RESTRICT ON UPDATE CASCADE',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%payment}}');
        $this->dropTable('{{%invoice}}');
        $this->dropTable('{{%data_meter}}');
        $this->dropTable('{{%periode}}');
    }

}
