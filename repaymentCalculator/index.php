<?php
ob_start();
include('inc/db.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Repayment Calculator</title>

    <!-- Bootstrap -->
    <link href="img/header.png" rel="icon">

    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body>

  <h1>Get Your Full Repayment Schedule Here!</h1><br><br><br>

<form action="index.php" method="POST">
  <?php
      if(isset($error)){
      echo "<span class='pull-right' style='color:red;'>$error</span>";
      } ?>

<div class="form-group">

    <label class="control-label" for="disabledInput">Start Date</label>
    <input class="form-control" name="start_date" id="disabledInput" type="date" placeholder="Start Date...">

</div><br>

<div class="form-group">
    <label class="control-label" for="readOnlyInput">Loan Amount</label>
    <input class="form-control" name="loan_amt" id="readOnlyInput" type="text" placeholder="Loan Amount...">

</div><br>

<div class="form-group">
  <label class="col-form-label col-form-label-lg" for="inputLarge">Installment Amount</label>
  <input class="form-control form-control-lg" name="installment_amt" type="text" placeholder="Installment Amount..." id="inputLarge">
</div><br>

<div class="form-group">
  <label class="col-form-label" for="inputDefault">Interest Rate</label>
  <input type="text" class="form-control" name="interest_rate" placeholder="Interest Rate..." id="inputDefault">
</div><br>

<div class="form-group">
  <label class="col-form-label" for="inputDefault">Installment Interval</label>
  <select class="form-control" name="installment_intvl" id="inputDefault">
    <option value="daily">Select An Option</option>
    <option value="daily">Daily</option>
    <option value="weekly">Weekly</option>
    <option value="monthly">Monthly</option>
  </select>

</div>
</div><br>

<input type="submit" value="Calculate Now" name="submit" class="btn btn-primary">

</form>

<br><br>

<?php
if(isset($_POST['submit'])){
  $rawdate = htmlentities($_POST['start_date']);
  $date = date('Y-m-d', strtotime($rawdate));
  $loan_amt = $_POST['loan_amt'];
  $installment_amt = $_POST['installment_amt'];
  $interest_rate = $_POST['interest_rate'];
  $installment_intvl = $_POST['installment_intvl'];

  if(empty($start_date) or empty($loan_amt) or empty($installment_amt) or empty($interest_rate) or empty($installment_intvl)){
        $error = "All field are required";
      }


$current_date = date('Y-m-d', strtotime('today'));

function debug_to_console( $data ) {
$output = $data;
if ( is_array( $output ) )
    $output = implode( ',', $output);

echo "<script>console.log( 'Debug Objects:  . $output . ' );</script>";
}
$daylen = 60*6*24;
$duration = date_diff(date_create($date), date_create($current_date));
$duration = $duration->format('%a');
debug_to_console($duration);
$interest_rate = $interest_rate/100;

switch($installment_intvl) {

  case "monthly":
  $duration = $duration / 30;
  $payment_tilldate = $installment_amt * $duration;
  $principal_off = $payment_tilldate/(1 + $interest_rate * $duration);
  $principal_left = $loan_amt - $principal_off;

  $payment_peryear = 12 * $installment_amt;
  $principal_peryear = $payment_peryear / (1 + $interest_rate);
  $time_left = $principal_left / $principal_peryear;
  $time_leftmonth = $time_left * 12;
  $total_interest = $principal_left * $interest_rate * $time_left;
  $total_amountleft = $principal_left + $total_interest;
  $time_left = $time_left * 365;

  $payoff_date = date('Y-m-d', strtotime($current_date) + (24*3600*$time_left));

?>
  <table class="table table-bordered table-striped table-hover">
    <thead>
      <tr>
        <th>Sl No.</th>
        <th>Installment Amount</th>
        <th>Installment date</th>
      </tr>
    </thead>
    <?php
    $date = $current_date;
  while($installment_amt <= $total_amountleft){
    $i = $installment_amt;
    $count = 1;
    for ($i = 0; $i <= $total_amountleft; $i++ ){
      echo "<tr>";
      echo "<td>";
      echo $count;
      echo "</td>";

      echo "<td>";
      echo $installment_amt;
      echo "</td>";

      echo "<td>";
      echo date('Y-m-d', strtotime(' + 1 month', strtotime($date)));
      echo "</td>";
      echo "</tr>";

      $count = $count + 1;
      $total_amountleft = $total_amountleft - $installment_amt;
      $date = date('Y-m-d', strtotime(' + 1 month', strtotime($date)));
    }
  }
  break; ?>
    </table>
    <?php


  case "weekly":
  $duration = $duration / 7;
  $payment_tilldate = $installment_amt * $duration;
  $duration = $duration * 7;
  $principal_off = $payment_tilldate/(1 + $interest_rate * ($duration/365));
  $principal_left = $loan_amt - $principal_off;

  $week_peryear = 365 / 7;
  $payment_peryear = $week_peryear * $installment_amt;
  $principal_peryear = $payment_peryear / (1 + $interest_rate);
  $time_left = $principal_left / $principal_peryear;
  $total_interest = $principal_left * $interest_rate * $time_left;
  $total_amountleft = $principal_left + $total_interest;
  $time_left = $time_left * 365;

  $payoff_date = date('Y-m-d', strtotime($current_date) + (24*3600*$time_left));

?>
  <table class="table table-bordered table-striped table-hover">
    <thead>
      <tr>
        <th>Sl No.</th>
        <th>Installment Amount</th>
        <th>Installment date</th>
      </tr>
    </thead>
    <?php
    $date = $current_date;
  while($installment_amt <= $total_amountleft){
    $i = $installment_amt;
    $count = 1;
    for ($i = 0; $i <= $total_amountleft; $i++ ){
      echo "<tr>";
      echo "<td>";
      echo $count;
      echo "</td>";

      echo "<td>";
      echo $installment_amt;
      echo "</td>";

      echo "<td>";
      echo date('Y-m-d', strtotime(' + 1 week', strtotime($date)));
      echo "</td>";
      echo "</tr>";

      $count = $count + 1;
      $total_amountleft = $total_amountleft - $installment_amt;
      $date = date('Y-m-d', strtotime(' + 1 week', strtotime($date)));

    }
  }
  break; ?>
    </table>
    <?php

    case "daily":

      $payment_tilldate = $installment_amt * $duration;
      $principal_off = $payment_tilldate/(1 + $interest_rate * ($duration/365));
      $principal_left = $loan_amt - $principal_off;

    $payment_peryear = $duration * $installment_amt;
    $principal_peryear = $payment_peryear / (1 + $interest_rate);
    $time_left = $principal_left / $principal_peryear;
    $total_interest = $principal_left * $interest_rate * $time_left;
    $total_amountleft = $principal_left + $total_interest;
    $time_left = $time_left * 365;

    $payoff_date = date('Y-m-d', strtotime($current_date) + (24*3600*$time_left));

  ?>
    <table class="table table-bordered table-striped table-hover">
      <thead>
        <tr>
          <th>Sl. No.</th>
          <th>Installment Amount</th>
          <th>Installment date</th>
        </tr>
      </thead>
      <?php
      $date = $current_date;
    while($installment_amt <= $total_amountleft){
      $count = 1;
      $i = $installment_amt;
      for ($i = 0; $i <= $total_amountleft; $i++ ){
        echo "<tr>";
        echo "<td>";
        echo $count;
        echo "</td>";

        echo "<td>";
        echo $installment_amt;
        echo "</td>";

        echo "<td>";
        echo date('Y-m-d', strtotime(' + 1 day', strtotime($date)));
        echo "</td>";
        echo "</tr>";

        $count = $count + 1;
        $total_amountleft = $total_amountleft - $installment_amt;
        $date = date('Y-m-d', strtotime(' + 1 day', strtotime($date)));

      }
    }
    break; ?>
      </table>
      <?php
}

}

?>

</body>
