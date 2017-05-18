<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Description of DataMigrationController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class DataMigrationController extends Controller
{

    public function actionMaster()
    {
        if (!Console::confirm('Are you sure you want to migrate data. Old data will be lose')) {
            return self::EXIT_CODE_NORMAL;
        }

        $command = Yii::$app->db->createCommand();

        $command->delete('{{%customer}}')->execute();
        $command->delete('{{%address}}')->execute();

        // address
        echo "Migrate address start...\n";
        $rows = require __DIR__ . '/data/address.php';
        $total = count($rows);
        Console::startProgress(0, $total);
        foreach ($rows as $i => $row) {
            $command->insert('{{%address}}', [
                'id' => $row[0],
                'code' => $row[1],
                'address' => $row[2],
            ])->execute();
            Console::updateProgress($i + 1, $total);
        }
        $command->resetSequence('{{%address}}')->execute();
        Console::endProgress();

        // customer
        echo "\nMigrate customer start...\n";
        $rows = require __DIR__ . '/data/customer.php';
        $total = count($rows);
        Console::startProgress(0, $total);
        foreach ($rows as $i => $row) {
            $command->insert('{{%customer}}', [
                'id' => $row[3],
                'code' => $row[0],
                'name' => $row[1],
                'address_id' => $row[4],
                'status' => $row[2],
            ])->execute();
            Console::updateProgress($i + 1, $total);
        }
        $command->resetSequence('{{%customer}}')->execute();
        Console::endProgress();
    }

    public function actionTransaction()
    {
        if (!Console::confirm('Are you sure you want to migrate data. Old data will be lose')) {
            return self::EXIT_CODE_NORMAL;
        }
        $cds = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $MONTH_FR = 24112;
        $MONTH_TO = 24208;

        $command = Yii::$app->db->createCommand();

        // periode
        echo "\nMigrate periode start...\n";
        $command->delete('{{%periode}}')->execute();
        $months = [];
        $i = 0;
        $total = $MONTH_TO - $MONTH_FR + 1;
        Console::startProgress(0, $total);
        for ($m = $MONTH_FR; $m <= $MONTH_TO; $m++) {
            $months[$m] = ++$i;
            list($M, $Y) = [($m - 1) % 12 + 1, (int) (($m - 1) / 12)];
            $FM = $M < 10 ? '0' . $M : $M;
            $fr = "$Y-$FM-01";
            $to = ($M === 2 && $Y % 4 === 0) ? "$Y-$FM-29" : "$Y-$FM-{$cds[$M]}";
            $command->insert('{{%periode}}', [
                'id' => $i,
                'name' => date('F, Y', strtotime($fr)),
                'date_from' => $fr,
                'date_to' => $to,
                'status' => 0
            ])->execute();
            Console::updateProgress($i, $total);
        }
        $command->resetSequence('{{%customer}}')->execute();
        Console::endProgress();

        echo "\nMigrate data meter start...\n";
        $command->delete('{{%data_meter}}')->execute();
        $command->resetSequence('{{%data_meter}}')->execute();

        $rows = require __DIR__ . '/data/data_meter.php';
        $total = (int) (count($rows) / 25) + 1;
        Console::startProgress(0, $total);
        foreach (array_chunk($rows, 25) as $i => $_rows) {
            foreach ($_rows as &$row) {
                $row[0] = $months[$row[0]];
            }
            $command->batchInsert('{{%data_meter}}', ['periode_id', 'meter', 'customer_id'], $_rows)->execute();
            Console::updateProgress($i + 1, $total);
        }
        Console::endProgress();
    }

    public function actionPrice()
    {
        if (!Console::confirm('Are you sure you want to migrate data. Old data will be lose')) {
            return self::EXIT_CODE_NORMAL;
        }

        $command = Yii::$app->db->createCommand();

        $command->delete('{{%price}}')->execute();
        $command->resetSequence('{{%price}}')->execute();

        // address
        echo "Migrate price start...\n";
        $rows = require __DIR__ . '/data/price.php';
        $total = count($rows);
        Console::startProgress(0, $total);
        foreach ($rows as $i => $row) {
            $command->insert('{{%price}}', $row)->execute();
            Console::updateProgress($i + 1, $total);
        }
        Console::endProgress();
    }
}
