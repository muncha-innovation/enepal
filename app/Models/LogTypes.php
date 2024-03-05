<?php

namespace App\Models;


class LogTypes
{
	static $AddProcessChildren = 'add-children-to-process';
	static $AddProcessToProduct = 'add-process-to-product';
	static $ViewDocument = 'view-document';
	static $DownloadDocument = 'download-document';
	static $CreatedProduct = 'created-product';
	static $CreatedProcess = 'created-process';
	static $EditedProduct = 'edited-product';
	static $EditedProcess = 'edited-process';
	static $UserCreated = 'user-created';
	static $UserUpdated = 'user-updated';
	static $PasswordChanged = 'password-changed';
	static $Search = 'search';
	static $RestoredProduct = 'restored-product';
	static $DeletedProduct = 'deleted-product';
	static $SoftDeletedProduct = 'deleted-process';
}