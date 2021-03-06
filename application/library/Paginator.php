<?php
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Paginator
 *
 * 分页
 */
trait Paginator
{

    /**
     * 分页
     *
     * @param Builder $builder eloquent builder对象
     * @param integer $perPage 每页大小
     * @param bool    $isAjax  是否是ajax分页,如果是ajax分页,将不返回分页连接
     *
     * @return array
     */
    public function paginate(Builder $builder, $perPage = null, $isAjax = false)
    {
        $total = $builder->toBase()->getCountForPagination();
        $page = $this->getRequest()->getQuery('page', 1);
        $perPage = $perPage ?: $builder->getModel()->getPerPage();
        $items = $builder->skip($perPage * ($page - 1) - 1)->take($perPage)->get();
        $totalPage = $total % $perPage == 0 ? $total / $perPage : intval($total / $perPage) + 1;
        $pagenator = [
            'items'     => $items,
            'totalPage' => $totalPage
        ];

        if (!$isAjax) {
            $links = $this->generateLinks($page, $totalPage);
            $pagenator['links'] = $links;
        }

        return $pagenator;
    }

    /**
     * 获取分页链接
     *
     * @param integer $currentPage 当前页
     * @param integer $totalPage   总页数
     *
     * @return string
     */
    public function generateLinks($currentPage, $totalPage)
    {
        $html = '<ul class="pagination">';
        if ($currentPage == 1) {
            $html .= "<li class=\"disabled\"><a href=\"javascript:void(0)\">&laquo;</a></li>";
        } else {
            $html .= "<li><a href=\"?page=". ($currentPage - 1) ."\">&laquo;</a></li>";
        }
        if ($totalPage < 10) {
            for ($i = 1; $i <= $totalPage; $i++) {
                $active = $i == $currentPage ? 'class="active"' : '';
                $html .= "<li $active><a href=\"?page=" . $i . "\">$i</a></li>";
            }
        } else {
            if ($currentPage > 3) {
                $start = $currentPage - 2;
            } else {
                $start = 1;
            }

            for ($i = $start; $i <= $currentPage; $i++) {
                $active = $i == $currentPage ? 'class="active"' : '';
                $html .= "<li $active><a href=\"?page=" . $i . "\">$i</a></li>";
            }

            for ($i = $currentPage + 1; $i <= $currentPage + 3 && $i <= $totalPage; $i++) {
                $active = $i == $currentPage ? 'class="active"' : '';
                $html .= "<li $active><a href=\"?page=" . $i . "\">$i</a></li>";
            }

            if ($totalPage - $currentPage >= 5) {
                $html .= "<li><a href='javascript:void(0)'>...</a></li>";
                $html .= "<li><a href=\"?page=" . $totalPage . "\">$totalPage</a></li>";
            }
        }
        if ($currentPage == $totalPage) {
            $html .= "<li class=\"disabled\"><a href=\"javascript:void(0)\">&raquo;</a></li>";
        } else {
            $html .= "<li><a href=\"?page=". ($currentPage + 1) ."\">&raquo;</a></li>";
        }
        return $html .= '</ul>';
    }

}