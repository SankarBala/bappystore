<div style="font-family: Helvetica,Arial,sans-serif;width:100%;overflow:auto;line-height:2">
    <div style="margin:50px auto;width:70%;padding:20px 0">
        <div style="border-bottom:1px solid #eee">
            <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">Bappy Store</a>
        </div>


        <p style="font-size:1.1em">Hi, {{ $user->name }}</p>
        <p>মোবাইল রিচার্জ এর সময় নিচের পিনটি ব্যবহার করুন। পিনটি কারও সাথে শেয়ার করবেন না। ধন্যবাদ</p>
        <h2
            style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
            {{ $user->recharge_pin }}</h2>
        <p style="font-size:0.9em;">Regards,<br />Bappy store</p>
        <hr style="border:none;border-top:1px solid #eee" />

    </div>
</div>
