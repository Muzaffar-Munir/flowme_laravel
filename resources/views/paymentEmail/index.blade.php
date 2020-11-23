<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
    <h2>Flow Me Payment</h2>   
    Hi {{$obj->first_name ? $obj->first_name:'' }} {{$obj->last_name ? $obj->last_name :''}}
    <p style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';font-size:16px;line-height:1.5em;margin-top:0;text-align:left">You have successfully submitted payment on our platform. {{$obj->total_cash?'Your net cash balance is '.$obj->total_cash : ''}}</p>
    <p style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';font-size:16px;line-height:1.5em;margin-top:0;text-align:left">Thanks for making Payment to Flow me</p>
    <p>Regards, Flow Me</p>
  </body>
</html>
