<title>Log In</title>
</head>
<body>
<div class="box">
<center>
<h1>Log In</h1>
<form method="POST">
<label>email:</label> <input name="email" type="text" /><br class="clear"/>
<label>password:</label> <input name="password" type="password" /><br class="clear" />
<input type="submit" value="Log In" />
</form>
<? if(isset($messages['error'])): ?>
   <span class="error"><?= $messages['error'] ?></span>
<? endif; ?>
</center>
</div>
</body>
</html>
