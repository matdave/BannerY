<?php

namespace BannerRevised\v2\Processors\Clicks;

class GetReferrers extends \modObjectGetListProcessor
{
    public $classKey = 'brevClick';
    public $languageTopics = array('bannerrevised:default');
    public $defaultSortField = 'clicks';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'bannerrevised.ad';

    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));
        $period = $this->getProperty('period');

        $conditions = array();

        if (!empty($period)) {
            if ($period == 'last month') {
                $conditions['clickdate:LIKE'] = strftime('%Y-%m', strtotime('first day of last month'));
            } else {
                $conditions['clickdate:LIKE'] = strftime($period) . '%';
            }
        }

        $c = $this->modx->newQuery($this->classKey);
        $c->select('COUNT(DISTINCT(referrer))');
        $c->andCondition($conditions);
        if ($c->prepare() && $c->stmt->execute()) {
            $rows = $c->stmt->fetchAll(\PDO::FETCH_COLUMN);
            $data['total'] = (int)reset($rows);

            $c = $this->modx->newQuery($this->classKey);
            $c->select('COUNT(id) as clicks, referrer');
            $c->andCondition($conditions);
            $c->groupby('referrer');
            $c->sortby('clicks', 'DESC');

            if ($limit > 0) {
                $c->limit($limit, $start);
            }
            $c->prepare();
            $c->stmt->execute();
            $data['results'] = $c->stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $data;
    }

    public function iterate(array $data)
    {
        $list = array();
        $this->currentIndex = 0;
        foreach ($data['results'] as $result) {
            $list[] = $result;
            $this->currentIndex++;
        }
        return $list;
    }
}
