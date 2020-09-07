<?php
	// 
    function realOverTime($strStartTime = '', $intOverDay = 0)
    {
        $arrData = canadler();
        include_once 'DateTimeDiff';
        $strYear = date('Y',strtotime($strStartTime));
        $calculator = new DateTimeDiff(
            date_create($strStartTime), //当前时间
            $arrData[$strYear]['holiday'],
            [DateTimeDiff::SATURDAY, DateTimeDiff::SUNDAY],
            $arrData[$strYear]['worker_day']
        );
        $calculator->addBusinessDays($intOverDay); 
        $afterBusinessDay = $calculator->getDate();
        return $afterBusinessDay;
    }

function customerReqTimeRealDay($strAddTime, $strInfoTime, $strReqTime)
    {
        /*
         * 点击完成所需要的处理时间
         * */
        # 根据是否上传资料时间 来判断取值
        $strBeginTime = $strInfoTime ? $strInfoTime : $strAddTime;
        $objStime = new DateTime(date('Y-m-d',$strBeginTime));
        # 客户要求时间就是结束时间
        $objFtime = new DateTime($strReqTime);
        $objDiff = $objFtime -> diff($objStime);
        # 获取2个时间之间的差值（没有去除节假日周末）
        $intDiff = $objDiff -> format('%d');
        $strAbs = $objDiff -> format('%R');
        # 如果结束时间小于开始时间 则拒绝处理
        if($strAbs == '+'){
            # 这里判断 方式底下while死循环
            $this -> _ajaxReturn(5,'调整时间不能小于添加时间或资料上传时间');
        }
        $arrConfData = canadler();
        $isBreak = true;
        if($intDiff == 0){
            $isBreak = false;
        }
        $intBusinessNum = 0;
        while($isBreak){
            $objStime -> modify("+1 day");
            if($objStime->format('Y-m-d') == $objFtime->format('Y-m-d')){
                $isBreak = false;
            }
            if(!$this -> isBusinessDay($objStime,$arrConfData)){
                $intBusinessNum++;
            }
        }
        return $intDiff - $intBusinessNum;
    }
    function canadler()
    {
        return [
            '2020' => [
                # 周末上班日期 通常是放假前后的调班
                'worker_day' => [
                    '2020-09-27',
                    '2020-10-10',
                ],
                # 节假日
                'holiday' => [
                    '2020-10-01',
                    '2020-10-02',
                    '2020-10-03',
                    '2020-10-04',
                    '2020-10-05',
                    '2020-10-06',
                    '2020-10-07',
                    '2020-10-08',
                ]
            ],
        ];
    }