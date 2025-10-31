Hi {{$name}},
<h4>ORDER INFORMATION</h4>
<p>________________________________<i style="color: #F00;">♥♥♥</i>__________________________________</p>
<p>Fullname: {{ $name }}</p>
<p>Phone: {{$phone }}</p>
<p>E-mail: {{$email }}</p>
<p>Address: {{$address }}</p>
<p>Order ID: <b>{{$cart_code }}</b></p>
<p>Payment: 
    <b><i style="color: #F00;">
    <?php
        $get_option = unserialize($option);
    	switch($cart_pay_method):
    		case 0:
    			echo "クレジットカード決済";
    		break;
    		default:
    			echo "銀行振込";
    	endswitch;
    ?>
    </i></b></p>
<p>Total: <b style="color: #F00;">¥{!!number_format($total)!!}</b></p>
<p>________________________________<i style="color: #F00;">♥♥♥</i>__________________________________</p>
<h4>ORDER DETAIL</h4>
<table width="100%" border="0" style="border: 1px solid #eae8e8;empty-cells: 0px;border-spacing: 0px;border-spacing: 0px;border-collapse: collapse;">
<tr>
    <th style="background: #adadad; text-align: center; padding:10px 15px; text-transform: uppercase; border: 1px solid #e5e5e5;">Name Product</td>
    <th style="background: #adadad; text-align: center; padding:10px 15px; text-transform: uppercase; border: 1px solid #e5e5e5;">Option</td>
    <th style="background: #adadad; text-align: center; padding:10px 15px; text-transform: uppercase; border: 1px solid #e5e5e5;">Quantity</td>
    <th style="background: #adadad; text-align: center; padding:10px 15px; text-transform: uppercase; border: 1px solid #e5e5e5;">Total</td>
</tr>
<tr>
    <td align="center" valign="middle" style="border: 1px solid #eaeaea;">
        E-Bike
    </td>
    <td align="center" valign="middle" style="border: 1px solid #eaeaea;">
        <?php
        if($get_option['logo_file'] != ''){
            $logo_file = asset('images/upload-logo/'.$get_option['logo_file']);
            $logo_file = '<a href="'.$logo_file.'" target="_blank">'.$logo_file.'</a>';
          } else{
            $logo_file = '';
          } 
        ?>
        <p>希望数字: {{$get_option['text']}}</p>
        <p>ロゴプリント希望: {{$logo_file}}</p>
        <p>数字またはロゴ貼付け箇所: {{$get_option['position_logo']}}</p>
        <p>ダブルシート: {{$get_option['doubleseat']}}</p>
        <p>LEDヘッドライト: {{$get_option['led']}}</p>
    </td>
    <td align="center" valign="middle" style="border: 1px solid #eaeaea;">
        <b><i style="color: #000;">{{$get_option['bike_quantity']}}<i></b>
        </td>
    <td align="center" valign="middle" style="border: 1px solid #eaeaea;">
        <b style="color: #F00;">¥{!!number_format($total)!!}</b>
    </td>
</tr>
<tr>
    <td colspan="4" align="center" valign="middle" style="border: 1px solid #eaeaea; padding:5px 10px;">
        <p><b>Total:</b> <b style="color: #F00;">¥{!!number_format($total)!!}</b></p>
    </td>
</tr>
</table>
<p>--</p>
<p>Thanks and Best Regards,</p>
<h4>E-Bike</h4>