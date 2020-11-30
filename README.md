# Last Page Redirect Plugin

This plugin is used to redirect to the user's last known page on your web application based on the referal domain.

## How it works

Visit the "Last Page Redirect" admin page to get started. (/wp-admin/admin.php?page=last-page-redirect)

### Adding Referal Domains

1. **Select *Add New* on the admin page.**
2. **Fill in the *Referal Domain* field.**
  - This field will be used to compare against the `document.referrer`.
  - This value should only be the domain level, not path. (i.e. https://www.test.com/)
3. **Select the *Method of Matching*.**
  - *Contains*: This will search the `document.referer` for a certain string. (i.e. If the value is set to *test.com* and the `document.referer` is `dev.test.com`, then the match would be true.)
  - *Exact Match*: The match will only be made if this value absolutely equals `document.referer`. (i.e. If the value is set to *http://test.com/* and the `document.referer` is *https://test.com* then the match would not be made.)
  
  ### Editing Referal Domains
  
  1. **Hover over the referal domain you'd like to edit.**
  2. **Click the *Edit* option that shows up after you hover over the domain.**
  3. **Make the changes.**
  4. **Click *Submit*.**
  
  ### Deleting Referal Domains
  1. **Hover over the referal domain you'd like to delete.**
  2. **Click the *Delete* option that shows up after you hover over the domain.**
  3. **Confirm you'd like to delete this domain by clicking *Yes, delete this redirect*. *There is no undoing this action.***
  
