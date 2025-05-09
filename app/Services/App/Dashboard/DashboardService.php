<?php

namespace App\Services\App\Dashboard;

use App\Models\Account;
use App\Models\Transaction;
use App\Services\App\AppService;
use App\Services\Core\Setting\SettingService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardService extends AppService
{
    public function getDefaultDashboardInfo()
    {
        $user = Auth::user();
        $totalInvitados = $user->referrals()->count();
        $totalConDeposito = $user->referrals()
            ->whereHas('referred.account.transactions', function ($query) {
                $query->where('type', 'deposit');
            })
            ->count();
        $totalSinDeposito = $totalInvitados - $totalConDeposito;
        $totalInvitados = ($totalInvitados == 0)? 1: $totalInvitados;
        $porcentajeInvitadosDepositos = ($totalInvitados - ($totalInvitados - $totalConDeposito)) / $totalInvitados;
        $porcentajeInvitadosSinDeposito = ($totalInvitados - ($totalConDeposito)) / $totalInvitados;
        $account = Account::where('id', auth()->user()->account->id)->first();
        $saldo = $account->balance;
        $bonus = Transaction::where('type', 'bonus')->sum('amount');
        $top_section_data = [
            ['label' => 'Saldo actual por Balance', 'number' => $saldo, 'icon' => 'trello'],
            ['label' => 'Ganado en bonus por Comisiónes', 'number' => $bonus, 'icon' => 'sidebar'],
            
        ];

        $depositCount = $user->account->transactions()->where('type', 'deposit')->count();
        $withdrawalCount = $user->account->transactions()->where('type', 'withdrawal')->count();
        $circle_top_section = [
            ['label' => 'Depositos', 'number' => $depositCount],
            ['label' => 'Retiros', 'number' => $withdrawalCount],
        ];

        $porcentajeInvitadosDepositos *= 100;
        $porcentajeInvitadosSinDeposito *= 100;
        $circle_bottom_section = [
            ['label' => 'Invitados con depositos', 'number' => $porcentajeInvitadosDepositos],
            ['label' => 'Invitados sin deposito', 'number' => $porcentajeInvitadosSinDeposito],

        ];

        // Contamos los usuarios que cumplen la condición;
        $bottom_section_data = [
            ['label' => 'Total invitados', 'number' => $totalInvitados, 'icon' => 'flag'],
            ['label' => 'Invitados con deposito', 'number' => $totalConDeposito, 'icon' => 'package'],
            ['label' => 'Invitados sin deposito', 'number' => $totalSinDeposito, 'icon' => 'users'],
        ];

        return [
            'topSectionData' => $top_section_data,
            'circleTopSection' => $circle_top_section,
            'circleBottomSection' => $circle_bottom_section,
            'bottomSectionData' => $bottom_section_data
        ];
    }

    public function getDateRange($filter)
    {

        $dt = Carbon::now();

        if ($filter == 'last_seven_days') {

            return ['range' => $this->lastSevenDaysRange($dt), 'filter' => $filter];
        } elseif ($filter == 'this_week') {

            return ['range' => $this->thisWeek($dt), 'filter' => $filter];
        } elseif ($filter == 'last_week') {

            return ['range' => $this->lastWeek($dt), 'filter' => $filter];
        } elseif ($filter == 'this_month') {

            return ['range' => $this->thisMonth($dt), 'filter' => $filter];
        } elseif ($filter == 'last_month') {

            return ['range' => $this->lastMonth(), 'filter' => $filter];
        } elseif ($filter == 'this_year') {

            return ['range' => $this->thisYear($dt), 'filter' => $filter];
        } elseif ($filter == 'total') {

            return ['range' => $this->total($dt), 'filter' => $filter];
        }
    }

    public function lastSevenDaysRange($dt)
    {
        $to = $dt->toDateString();
        $from = $dt->subDays(7)->toDateString();
        return ['to' => $to, 'from' => $from];
    }

    public function thisWeek($dt)
    {
        $to = $dt->startOfWeek()->toDateString();
        $from = $dt->endOfWeek()->toDateString();
        return ['to' => $to, 'from' => $from];
    }

    public function lastWeek($dt)
    {
        $to = $dt->toDateString();
        $from = $dt->subDays(7)->toDateString();

        return ['to' => $to, 'from' => $from];
    }

    public function thisMonth($dt)
    {

        $to = $dt->firstOfMonth()->toDateString();
        $from = $dt->lastOfMonth()->toDateString();

        return ['to' => $to, 'from' => $from];
    }

    public function lastMonth()
    {
        $to = new Carbon('first day of last month');
        $from = new Carbon('last day of last month');

        $to = $to->toDateString();
        $from = $from->toDateString();

        return ['to' => $to, 'from' => $from];
    }

    public function thisYear($dt)
    {
        $to = $dt->copy()->startOfYear()->toDateString();
        $from = $dt->copy()->endOfYear()->toDateString();

        return ['to' => $to, 'from' => $from];
    }

    public function total($dt)
    {
        $to = $dt->toDateString();
        $from = Carbon::parse('first day of January 2017')->toDateString();

        return ['to' => $to, 'from' => $from];
    }

    public function audienceOverviewChart($to, $from, $filter)
    {
        $users = 0;
        $visitors = 0;
        $users_count_list = [];
        $visitors_count_list = [];
        $chart_data = [];

        $range = [
            'to' => $to,
            'from' => $from
        ];

        $chart_elements = [
            'element1' => 'Retiros',
            'element2' => 'Depositos'
        ];

        $userId = auth()->user()->id; // ID del usuario que deseas filtrar

        $results = DB::table('transactions')
            ->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(CASE WHEN type = "withdrawal" THEN 1 ELSE 0 END) as Retiros'),
                DB::raw('SUM(CASE WHEN type = "deposit" THEN 1 ELSE 0 END) as Depositos')
            )
            ->join('accounts', 'transactions.accountId', '=', 'accounts.id')
            ->where('accounts.userId', $userId)
            ->groupBy(DB::raw('DATE(transactions.created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        dd($results);
        // Convertir a un arreglo si es necesario
        $formattedResults = $results->map(function ($item) {
            return [
                'date' => $item->date,
                'Retiros' => (int) $item->Retiros,
                'Depositos' => (int) $item->Depositos,
            ];
        })->toArray();

        $sample = $this->getDemoData($chart_elements);
        $filter_data = $this->doFilter($formattedResults, $range);


        foreach ($filter_data as $item) {

            $user_count = $users + $item['Retiros'];
            $visitor_count = $visitors + $item['Depositos'];
            array_push($users_count_list, $item['Retiros']);
            array_push($visitors_count_list, $item['Depositos']);
        }

        $user = [
            'title' => 'Retiros',
            'fill' => false,
            'borderWidth' => 1.5,
            'borderColor' => '#f7531e',
            'backgroundColor' => '#f7531e',
            'data' => $users_count_list
        ];

        array_push($chart_data, $user);

        $visitor = [
            'title' => 'Depositos',
            'fill' => false,
            'borderWidth' => 1.5,
            'borderColor' => '#368cd5',
            'backgroundColor' => '#368cd5',
            'data' => $visitors_count_list
        ];

        array_push($chart_data, $visitor);

        return [
            'user' => $user_count,
            'visitor' => $visitor_count,
            'chartData' => $chart_data,
            'labels' => $this->getLebelsForDashboardChart($filter, $to, $from),
            'chartFilterOptions' => $this->getAudienceFilterOptions()
        ];
    }

    public function getAudienceFilterOptions()
    {
        $chart_filter_options = [
            ['id' => 'this_week', 'value' => 'This week'],
            ['id' => 'last_week', 'value' => 'Last week'],
            ['id' => 'this_month', 'value' => 'This month'],
            ['id' => 'last_month', 'value' => 'Last month'],
            ['id' => 'this_year', 'value' => 'This year'],
            ['id' => 'total', 'value' => 'Total']
        ];
        return $chart_filter_options;
    }

    public function getLebelsForDashboardChart($filter, $to = null, $from = null)
    {
        if ($filter == 'last_seven_days' || $filter == 'this_week' || $filter == 'last_week') {

            $labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        } elseif ($filter == 'this_month' || $filter == 'last_month') {
            $labels = $this->getDatesBetweenTowDates($to, $from);
        } else {
            $labels = ['Jan', 'Feb', 'Mar', 'App', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        }
        return $labels;
    }

    public function getDatesBetweenTowDates($to, $from)
    {
        $start = Carbon::parse($to);
        $end = Carbon::parse($from);
        $dateRange = CarbonPeriod::create($start, $end);

        $settings = resolve(SettingService::class)
            ->getFormattedSettings();

        $dates = [];
        foreach ($dateRange as $date) {
            $dates[] = $date->format($settings['date_format']);
        }
        return $dates;
    }

    public function doFilter($array, $range)
    {

        $this->range = $range;

        return array_filter($array, array($this, 'getFiltered'));
    }

    public function getFiltered($element)
    {
        $start_date = Carbon::createFromFormat('Y-m-d', $this->range['from']);
        $end_date = Carbon::createFromFormat('Y-m-d', $this->range['to']);
        $check_date = Carbon::createFromFormat('Y-m-d', $element['date']);

        return $check_date->between($start_date, $end_date);
    }

    public function browserOverviewChart($to, $from, $filter)
    {
        $chrome = 0;
        $firefox = 0;
        $opera = 0;
        $chrome_count_list = [];
        $firefox_count_list = [];
        $opera_count_list = [];
        $chart_data = [];

        $range = [
            'to' => $to,
            'from' => $from
        ];

        $chart_elements = [
            'element1' => 'chrome',
            'element2' => 'firefox',
            'element3' => 'opera'
        ];

        $sample = $this->getDemoData($chart_elements);

        $filter_data = $this->doFilter($sample['data'], $range);


        foreach ($filter_data as $item) {

            $chrome_count = $chrome + $item['chrome'];
            $firefox_count = $firefox + $item['firefox'];
            $opera_count = $opera + $item['opera'];
            array_push($chrome_count_list, $item['chrome']);
            array_push($firefox_count_list, $item['firefox']);
            array_push($opera_count_list, $item['opera']);
        }

        $chrome = [
            'title' => 'chrome',
            'pointRadius' => 0,
            'borderWidth' => 0,
            'borderColor' => 'rgba(3, 90, 166, 0.7)',
            'backgroundColor' => 'rgba(3, 90, 166, 0.7)',
            'data' => $chrome_count_list
        ];
        array_push($chart_data, $chrome);

        $firefox = [
            'title' => 'firefox',
            'pointRadius' => 0,
            'borderWidth' => 0,
            'borderColor' => 'rgba(64, 186, 213, 0.7)',
            'backgroundColor' => 'rgba(64, 186, 213, 0.7)',
            'data' => $firefox_count_list
        ];
        array_push($chart_data, $firefox);

        $opera = [
            'title' => 'opera',
            'pointRadius' => 0,
            'borderWidth' => 0,
            'borderColor' => 'rgba(41, 199, 172, 0.7)',
            'backgroundColor' => 'rgba(41, 199, 172, 0.7)',
            'data' => $opera_count_list
        ];
        array_push($chart_data, $opera);


        return [
            'chrome' => $chrome_count,
            'firefox' => $firefox_count,
            'opera' => $opera_count,
            'chartData' => $chart_data,
            'labels' => $this->getLebelsForDashboardChart($filter, $to, $from),
            'chartFilterOptions' => $this->getAudienceFilterOptions()
        ];
    }

    public function getDemoData($chart_elements)
    {
        //Sample data set

        $data = [];
        $temp = [];

        $previous = Carbon::now()->subDays(365);

        $first_day_of_last_year = "first day of January " . "" . $previous->year . "";

        $first_day = Carbon::parse($first_day_of_last_year);


        /* *
         *
         * This will generate a data set of date, subscriber, unsubscriber
         *
         * date range in data set contains date from last year((dynamic)) to 1 year after of current year(dynamic)
         * subscriber amount random
         * unsubscriber amount random
         * In total of 500 data
         *
         * each contain dynamic value
         * */

        for ($case = 1; $case < 500; $case++) {

            if ($case < 25) {
                $first_day = $first_day->addDays($case);
            } else {
                $first_day = $first_day->addDays(1);
            }

            $temp['date'] = $first_day->toDateString();

            foreach ($chart_elements as $key => $item) {
                $temp[$chart_elements[$key]] = mt_rand(30, 99);
            }

            array_push($data, $temp);
        }

        return [
            'data' => $data
        ];
    }
}
