<?php
  if($_GET['action'] == 'duplicate'){
    echo '<div class="Text-Red-14" id="alertmsg">Email address already exists. Please enter different email address.</div>';
  }
  if($_GET['action'] == 'incomplete'){
    echo '<div class="Text-Red-14" id="alertmsg">Please complete the form before submit.</div>';
  }
?>
<form method="post" action="process.php" name="signup">
<!--<form method="post" action="http://localhost/pommo/user/process.php" name="signup">-->
<fieldset>
<div class="notes">
<p>Fields marked * are required.</p>
</div>


<!--	BEGIN INPUT FOR REQUIRED FIELD "Name" -->
<div>
<label for="field28">*First Name:</label>
<input type="text" name="d[28]" id="field28" maxlength="60" />
</div>
<div>
<label for="field29">*Last Name:</label>
<input type="text" name="d[29]" id="field29" maxlength="60" />
</div>

<!--	Email field must be named "Email" -->
<div>
<label for="email">*Your Email:</label>
<input type="text" name="Email" id="email" maxlength="60" />
</div>
<!--	BEGIN INPUT FOR FIELD "HomeStreet" -->
<div>
<label for="field4">Street 1:</label>
<input type="text" name="d[4]" id="field4" maxlength="60" />
</div>

<!--	BEGIN INPUT FOR FIELD "HomeStreet 2" -->
<div>
<label for="field5">Street 2:</label>
<input type="text" name="d[5]" id="field5" maxlength="60" />
</div>

<!--	BEGIN INPUT FOR FIELD "HomeStreet 3" -->
<div>
<label for="field6">Street 3:</label>
<input type="text" name="d[6]" id="field6" maxlength="60" />
</div>

<!--	BEGIN INPUT FOR REQUIRED FIELD "State" -->
<div>
<label for="field20">*State:</label>

<select name="d[20]" id="field20">
<option>Please choose...</option>
<option value="NSW"> NSW</option>

<option value="QLD"> QLD</option>
<option value="VIC"> VIC</option>
<option value="ACT"> ACT</option>
<option value="TAS"> TAS</option>
<option value="NT"> NT</option>
<option value="SA"> SA</option>

<option value="WA"> WA</option>
<option value="Others"> Others</option>
</select>
</div>

<!--	BEGIN INPUT FOR REQUIRED FIELD "Country" -->
<div>
<label for="field19">*Country:</label>

<select name="d[19]" id="field19">
<option>Please choose...</option>

<option value="AU"> AU</option>
<option value="NZ"> NZ</option>
</select>
</div>

<!--	BEGIN INPUT FOR REQUIRED FIELD "Postcode" -->
<div>
<label for="field12">*Postcode:</label>
<input type="text" name="d[12]" id="field12" maxlength="60" />
</div>

<!--	BEGIN INPUT FOR FIELD "Phone No" -->

<div>
<label for="field21">Phone No.:</label>
<input type="text" name="d[21]" id="field21" maxlength="60" />
</div>

<!--	BEGIN INPUT FOR FIELD "Codeword" -->

<div>
<label for="field26">Codeword:</label>
<input type="text" name="d[26]" id="field26" maxlength="60" />
</div>

<!--	BEGIN INPUT FOR FIELD "group" -->

<input type="hidden" name="d[27]" id="field27" maxlength="60" value="AYAM" />


</fieldset>

<div id="buttons">

<!--  *** DO NOT CHANGE name="pommo_signup" ! ***
	  If you'd like to change the button text change the "value=" text. -->
	  
<input type="hidden" name="d[23]" value="<?php echo $_GET['referrer']; ?>" />
<input type="hidden" name="pommo_signup" value="true" />
<input type="submit" value="Subscribe" />
<a href="../privacy-policy.html" target="_blank" style="font-family: Lucida Sans Unicode, Tahoma, Verdana;color:#039;font-size:12px;text-decoration:none;">Please read our Privacy Policy</a></div>

</form>