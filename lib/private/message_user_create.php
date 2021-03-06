<?php

    function msgUserCreate( $aRequest )
    {
        require_once dirname(__FILE__).'/../config/config.php';
        $Out = Out::getInstance();

        if ( ALLOW_REGISTRATION )
        {
            $Salt = UserProxy::generateKey32();
            $NativeBinding = new NativeBinding();

            $HashedPassword = $NativeBinding->hash( $aRequest['pass'], $Salt, 'none' );

            $PublicMode = defined('PUBLIC_MODE') && PUBLIC_MODE;
            $DefaultGroup = ($PublicMode) ? 'member' : 'none';

            $Out->pushValue('publicmode', $PublicMode);
            $UserId = UserProxy::createUser($DefaultGroup, 0, 'none', $aRequest['name'], $HashedPassword, $Salt);

            if ( $UserId === false )
            {
                $Out->pushError(L('NameInUse'));
            }
            else
            {
                Log::getInstance()->create(LOG_TYPE_USER, $UserId, [
                    'name' => $aRequest['name'],
                ]);
            }
        }
        else
        {
            $Out->pushError(L('AccessDenied'));
        }
    }

?>
