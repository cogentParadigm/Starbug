Starbug implements a sort of two tier access control system. There are programmatic entry points which you can use to implement custom access control logic, or you can fall back to the built in Role Based Access Control (RBAC) system. This is based on a system of grants called `permits`. These are stored in the permits table.

## Groups and Ownership

Users in Starbug include a groups field. This field can also be applied to other entities if you wish to use common group associations to control access. All entities within Starbug have an owner field that represents who created the record.

| Field | Description |
|-------|-------------|
| `owner` | The owner field references a user ID to indicate that users ownership of the record. |
| `groups` | Groups is a multiple reference to the groups taxonomy. Each record can have one or more groups. |

## Permits

Permits are grants that you can query against. For example, you can create a permit to grant users within a certain group access to update pages that are tagged with the same group. You can then do a query of pages which the logged in user has access to update. The permits can utilize groups and ownership by default, but you can also configure custom fields to be usable in permits. Let's look at the different components of a permit.

| Field | Description |
|-------|-------------|
| `related_table` | The model |
| `action` | The action. This will be the name of a function such as `create` or `delete`. It can also be `read` |
| `role` | The role that this applies to. See roles below |
| `priv_type` | This can `table`, `global`, or `%` (any). If you are acting on a specific record, you need global permits. Otherwise, you need table permits. |
| `object_deleted` | You would use this field to restrict the permit based on the value of `deleted` on the record. |
| `user_groups` | You would use this field to apply this permit to a specific group of users |

### Roles

| Role | Description |
|------|-------------|
| `everyone` | This means everyone. If you set `user_groups`, it's everyone within the group |
| `owner` | The owner role is fulfilled when a users id is in the owner field of the target record or records |
| `self` | This would only apply to a user operating on themselves. For example, doing an `update_profile` |
| `groups` | This would apply between users and objects that share the same groups |

### Model and action

Permits are granted by model and action. For example, you might grant access to the `Users::login` function by setting the related_table to `users` and the action to `login`. It's important to note that you can call these actions independently of the URL you are visiting.

### Type (priv_type)

Possible options are table, and global. Table applies to the table like directory permissions such as creating an entry. Global applies to records within the table and can be used for actions that relate to specific objects (edit, delete). The type of permit that is looked for is based on whether or not an id is included in the POST data.

### Defining a permit

Permits are defined in migrations simply by adding rows:

```php
<?php
$this->schema->addRow("permits", [/* fields */]);
```

## Examples

This is what the login permit looks like.

```php
<?php
$this->schema->addRow(
	"permits",
	[
		"related_table" => "users",
		"action" => "login",
		"role" => "everyone",
		"priv_type" => "table"
	]
);
```

The POST data might look like this:

```json
{
  "action[users]":"login",
  "users[email]":"ali@neonrain.com",
  "users[password]":"weakpassword"
}
```

The update profile permit:

```php
<?php
$this->schema->addRow(
	"permits",
	[
		"related_table" => "users",
		"action" => "update_profile",
		"role" => "self",
		"priv_type" => "global"
	]
);
```

The priv_type is global and the POST data includes an id:

```json
{
  "action[users]":"update_profile",
  "users[id]":"5",
  "users[first_name]":"Ali",
  "users[last_name]":"Gangji"
}
```

## Custom object access fields

The content module defines a pages table form CMS content with a published flag. We can grant access based on the published status by adding the `object_access` property to the field.

```php
<?php
$this->schema->addColumn("pages",
	["published", "type" => "bool", "object_access" => true]
);
```
Then you could grant read access to published pages by defining the following permit.

```php
<?php
$this->schema->addRow(
	"permits",
	[
		"related_table" => "pages",
		"action" => "read",
		"role" => "everyone",
		"priv_type" => "global",
		"object_published" => "1"
	]
);
```

Getting a little bit more sophisticated we can use the groups role instead to allow group associations to control access.

```php
<?php
$this->schema->addRow(
	"permits",
	[
		"related_table" => "pages",
		"action" => "read",
		"role" => "groups",
		"priv_type" => "global",
		"object_published" => "1"
	]
);
```

In other words, if you want to restrict a pages entry to `admin` users, apply the `admin` group to that record.
