<?php


function build_calendar($month,$year,$dateArray) {

    include('inc/EventPosts.class.php');



    $events = new EventPosts();


     // Create array containing abbreviations of days of week.
     $daysOfWeek = array('S','M','T','W','T','F','S');

     // What is the first day of the month in question?
     $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

     // How many days does this month contain?
     $numberDays = date('t',$firstDayOfMonth);

     // Retrieve some information about the first day of the
     // month in question.
     $dateComponents = getdate($firstDayOfMonth);

     // What is the name of the month in question?
     $monthName = $dateComponents['month'];

     // What is the index value (0-6) of the first day of the
     // month in question.
     $dayOfWeek = $dateComponents['wday'];

    // print_r($dateComponents);

     // Calendar navigation
     $calendar = '<div><a href="'.$_SERVER['PATH_INFO'].'?nav=prev&month='.$dateComponents['mon'].'&year='.$dateComponents['year'].'">prev<a/><a href="'.$_SERVER['PATH_INFO'].'?nav=next&month='.$dateComponents['mon'].'&year='.$dateComponents['year'].'">next<a/></div>';

     // Create the table tag opener and day headers

     $calendar .= "<table class='calendar'>";
     $calendar .= "<caption>$monthName $year</caption>";
     $calendar .= "<tr>";

     // Create the calendar headers

     foreach($daysOfWeek as $day) {
          $calendar .= "<th class='header'>$day</th>";
     } 

     // Create the rest of the calendar

     // Initiate the day counter, starting with the 1st.

     $currentDay = 1;

     $calendar .= "</tr><tr>";

     // The variable $dayOfWeek is used to
     // ensure that the calendar
     // display consists of exactly 7 columns.

     if ($dayOfWeek > 0) { 
          $calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>"; 
     }
     
     $month = str_pad($month, 2, "0", STR_PAD_LEFT);
  
     while ($currentDay <= $numberDays) {

          // Seventh column (Saturday) reached. Start a new row.

          if ($dayOfWeek == 7) {

               $dayOfWeek = 0;
               $calendar .= "</tr><tr>";

          }
          
          $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
          
          $date = "$year-$month-$currentDayRel";

          $calendar .= "<td class='day' rel='$date'>$currentDay";

          foreach ($events->get_display_posts() as $key => $value) {
            if(date('Y-m-d', $key) == $date){
              $calendar .= get_the_title($value);
            }
          }

          $calendar .= "</td>";

          // Increment counters
 
          $currentDay++;
          $dayOfWeek++;

     }
     
     

     // Complete the row of the last week in month, if necessary

     if ($dayOfWeek != 7) { 
     
          $remainingDays = 7 - $dayOfWeek;
          $calendar .= "<td colspan='$remainingDays'>&nbsp;</td>"; 

     }
     
     $calendar .= "</tr>";

     $calendar .= "</table>";

     return $calendar;

}

function trigger_build_calendar(){

    if(isset($_GET['nav'])){

      // next month
      if($_GET['nav'] == 'next'){

       

          $calendarDate = mktime(0,0,0,$_GET['month'],1,$_GET['year']);

          $calendarDate = date('Y-m-d',$calendarDate);

          $calendarDate = new DateTime($calendarDate);

          $calendarDate->modify('next month');

          $dateComponents = getdate(strtotime($calendarDate->format('Y-m-d')));


        

      // prev month
      }elseif($_GET['nav'] == 'prev'){

         $calendarDate = mktime(0,0,0,$_GET['month'],1,$_GET['year']);

          $calendarDate = date('Y-m-d',$calendarDate);

          $calendarDate = new DateTime($calendarDate);

          $calendarDate->modify('last month');

          $dateComponents = getdate(strtotime($calendarDate->format('Y-m-d')));

      }

    }else{

     $dateComponents = getdate();

    }

     $month = $dateComponents['mon'];                  
     $year = $dateComponents['year'];

    // print_r($dateComponents);



     echo build_calendar($month,$year,$dateArray);



}

function check_page(){



     //print_r($WP:);
     if(is_page('events')){
           // TIME UI
        wp_enqueue_script(
            'jquery-timepicker',
            SCRIPTS.'jquery-timepicker-master/jquery.timepicker.js',
            array('jquery'),
            false,
            false
        );


          add_filter ('the_content', 'trigger_build_calendar');
     }
}

add_action('the_post','check_page');



?>