<?
//AddEventHandler("main", "OnEndBufferContent", "OnEndBufferContentFunction");
function OnEndBufferContentFunction(&$content)
{
    $main = new Content($content);
    $content = $main->replace();
}

class Content
{
    private $regex = '/<spamwords id="(.*?)"><\/spamwords>/';
    private $content;
    private $currPage;

    public function __construct($content)
    {
        $this->content = $content;
        $this->currPage = (new CMain)->GetCurDir();
    }

    protected function hideTag($string)
    {
        return '<hide content="'.$string.'"></hide>';
    }

    protected function words()
    {
        if($this->isAdminPage() || empty(preg_match($this->regex, $this->content,$match)))
            return null;

        if($spam = (new IBlockSpamWords)->setId($match[1])->get())
            return $spam["PROPS"]["WORDS"]["VALUE"];

        return null;
    }

    public function replace()
    {
        $words = $this->words();

        if(empty($words))
            return $this->content;

        $replace = [];
        foreach ($words as $word)
            $replace[] = $this->hideTag($word);

        return str_replace($words, $replace, $this->content);
    }

    private function isAdminPage()
    {
        return $this->currPage === '/bitrix/admin/';
    }
}

class IBlockSpamWords
{
    private $id;
    private $code = 'spam-words';

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function get()
    {
        if(!CModule::IncludeModule("iblock"))
            return null;

        $arSelect = ["ID", "IBLOCK_ID", "PROPERTY_*"];
        $arFilter = ["IBLOCK_CODE" => $this->code, "ID" => $this->getId()];
        $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        if($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();

            $arFields["PROPS"] = $arProps;
            return $arFields;
        }

        return null;
    }
}
