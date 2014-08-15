**poMMo** currently supports two types of **Dynamic Elements**.

1. **System Elements** and

2. **User Elements**

Both can be embedded into an email in the following format.

* **[\[!system-element-name]]** - where system-element-name is from the list below.
* **[\[user-element-name]]** - where user-element-name may be created by you detailed later in this page.

The only difference between the two types of elements is that a **System Element** is preceded by an **exclamation mark**.

### System Elements in poMMo

These are hard coded to provide commonly used feedback functions within the recipients email.

* [\[!unsubscribe]] - this will be replaced with a single link which contains the unsubscribe URL, the recipients email address and verification code in the recipients email.
* [\[!webpage]] - this will be replaced with a URL link in the recipients email to allow the user to view your email in a web browser.

#### Examples to embed in your email:

```
    To view this email in a browser, click this link. [[!webpage]]
```
```
    If you wish to unsubscribe from this email, click here. [[!unsubscribe]]
```

### User Elements in poMMo

These are created by you by going to **Setup->Subscriber Fields**

* Create a new field name
* Give it a field type and then click add.
* A **parameters window** will pop up.
* Under **Form Name** enter the name of the **User Element** that you will later embed in your email.
* Let us say you create a form name as **FirstName** with no spaces.
* Do not use the square brackets. The system will handle that.
* Scroll to the bottom of the Parameters window and click **Update**.

Now, the next time you compose an email or edit a template, you will be able to embed your new element called **FirstName** in the following way.

#### Examples
* **Note:** User Elements do not have a preceding exclamation mark.

```
    Dear [[FirstName]]

    We just found out that today is your birthday and we would like to wish you the very best for the year ahead.

    Regards

    The poMMo Team
    To view this email in a browser, click this link. [[!webpage]]

```

