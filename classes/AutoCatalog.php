<?php

class AutoCatalog
{
    public SimpleXMLElement $xml;

    public function __construct(string $data, bool $dataIsURL)
    {
        // обернул в try для случая если SimpleXMLElement не сможет распарсить файл или строку
        try {
            if ($dataIsURL) { //Если данные - это ссылка или путь к файлу
                if (file_exists($data)) { // то ножно проверить, есть ли этот файл
                    $this->xml = new SimpleXMLElement($data, 0, $dataIsURL);
                    return;
                }
                throw new Exception("File not exists!", 1); // Если файла нет - возвращаем ошибку
            }
            // А на случай если $data - это xml строка, тогда просто передаём строку
            $this->xml = new SimpleXMLElement($data, 0, $dataIsURL);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Необходимо для получения нужного индекса массива offer
    public function get_id_index_pair(): array
    {
        $pair = array();
        $items = (array) $this->xml->offers;
        for ($i = 0; $i < count($items["offer"]); $i++){
            $pair[intval($items["offer"][$i]->id)] = $i;
        }
        return $pair;
    }

    // SimpleXMLElement у меня очень туго переводился в массив
    function offer_to_array(SimpleXMLElement $offer): array {
        $x = array();
        $x["id"] = (int) $offer->id;
        $x["mark"] = (string) $offer->mark;
        $x["model"] = (string) $offer->model;
        $x["generation"] = (string) $offer->generation;
        $x["year"] = (int) $offer->year;
        $x["run"] = (int) $offer->run;
        $x["color"] = (string) $offer->color;
        $x["body-type"] = (string) $offer->{"body - type"};
        $x["engine-type"] = (string) $offer->{"engine - type"};
        $x["transmission"] = (string) $offer->transmission;
        $x["gear-type"] = (string) $offer->{"gear - type"};
        $x["generation_id"] = (int) $offer->generation_id;
        return $x;
    }

    // Метод для получения массива поля id всех элементов xml-выгрузки
    public function get_id_array(): array
    {
        $x = array();
        foreach ($this->xml->offers->offer as $offer) {
            $x[] = (int)$offer->id;
        }
        return $x;
    }
}
