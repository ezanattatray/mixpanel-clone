<title>Register</title>
</head>
<body>
<div class="box">
<center>
<h1>Register</h1>
<form method="POST">
<label>email:</label> <input name="email" type="text" /><br />
<label>password:</label> <input name="password" type="password" /><br />
<input type="submit" value="Register" />
</form>
<? if(isset($messages['success'])): ?>
   <span class="success"><?= $messages['success'] ?></span>
<? endif; ?>
<? if(isset($messages['error'])): ?>
   <span class="error"><?= $messages['error'] ?></span>
<? endif; ?>
</body>
</center>
</div>
</html>
