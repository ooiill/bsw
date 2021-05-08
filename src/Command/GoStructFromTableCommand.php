<?php

namespace App\Command;

use Leon\BswBundle\Command\LogicHandlerCommand;
use Leon\BswBundle\Component\Helper;
use Symfony\Component\Console\Input\InputOption;

class GoStructFromTableCommand extends LogicHandlerCommand
{
    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'go',
            'keyword' => 'struct-from-table',
            'info'    => '将表输出为 Golang 的 Entity 包文件',
        ];
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return array_merge(
            parent::args(),
            [
                'table' => [null, InputOption::VALUE_REQUIRED, 'Table name'],
            ]
        );
    }

    /**
     * @return bool
     */
    public function pass(): bool
    {
        return true;
    }

    /**
     * 业务逻辑
     *
     * @throws
     */
    public function handler()
    {
        if (empty($this->params->table)) {
            $this->output->writeln("<error> Table name is not set </error>");

            return;
        }

        $ImportPkg = null;
        $UseCarbon = false;
        $TableStruct = [];
        $TableName = Helper::camelToUnder($this->params->table);
        $TableNameCamel = Helper::underToCamel($TableName, false);

        $document = $this->web->mysqlSchemeDocument($TableName);
        if (empty($document['fields'])) {
            $this->output->writeln("<error> Table not exists in doctrine (database) </error>");

            return;
        }

        foreach ($document['fields'] as $item) {
            if (in_array($item['name'], ['id', 'add_time', 'update_time', 'state'])) {
                continue;
            }
            switch (strtoupper($item['type'])) {
                case 'TINYINT':
                    $type = 'int8';
                    break;
                case 'SMALLINT':
                case 'MEDIUMINT':
                    $type = 'int';
                    break;
                case 'INT':
                case 'INTEGER':
                    $type = 'int64';
                    break;
                case 'FLOAT':
                case 'DOUBLE':
                case 'DECIMAL':
                    $type = 'float64';
                    break;
                case 'TIMESTAMP':
                case 'DATETIME':
                case 'DATE':
                case 'TIME':
                case 'YEAR':
                    $type = 'carbon.Carbon';
                    if ($UseCarbon == false) {
                        $ImportPkg .= "\"github.com/golang-module/carbon\"";
                        $UseCarbon = true;
                    }
                    break;
                default:
                    $type = 'string';
            }
            $TableStruct[] = sprintf(
                "%s %s `json:\"%s\"` // %s",
                Helper::underToCamel($item['name'], false),
                $type,
                $item['name'],
                $item['comment'],
            );
        }

        $variables = [
            'TableName'      => $TableName,
            'TableNameCamel' => $TableNameCamel,
            'ImportPkg'      => $ImportPkg,
            'TableStruct'    => implode("\n    ", $TableStruct),
        ];
        $file = $this->web->getFilePath('{TableName}.go.chunk', 'src/Command/Chunk', false);
        $content = file_get_contents($file);
        foreach ($variables as $vk => $vv) {
            $content = str_replace("{{$vk}}", $vv, $content);
        }

        print_r($content);
        $this->output->writeln("<info> \n↑↑↑ 数据表 {$TableName} 生成结构体完成. </info>");
    }
}
