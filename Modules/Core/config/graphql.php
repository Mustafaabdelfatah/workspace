<?php

return [
    'auth' => [
        'query' => [
            Modules\Core\app\GraphQL\Queries\Permission\PermissionsTreeQuery::class,
            Modules\Core\app\GraphQL\Queries\Permission\GetGroupQuery::class,
            Modules\Core\app\GraphQL\Queries\Permission\GetGroupsQuery::class,
            Modules\Core\GraphQL\Queries\User\GetUsersQuery::class,
            Modules\Core\GraphQL\Queries\User\GetModulesQuery::class,
            Modules\Core\GraphQL\Queries\Translation\GetTranslationsQuery::class,
            Modules\Core\GraphQL\Queries\Translation\DownloadTranslationTemplateQuery::class,
            Modules\Core\GraphQL\Queries\Translation\ExportTranslationDataQuery::class,
            Modules\Core\GraphQL\Queries\Translation\GetTranslationUploadsHistoryQuery::class,
            Modules\Core\GraphQL\Queries\Module\GetModulesQuery::class,
            Modules\Core\GraphQL\Queries\Notification\GetSentNotificationsQuery::class,
            Modules\Core\GraphQL\Queries\Notification\GetNotificationsQuery::class,
            Modules\Core\GraphQL\Queries\Notification\UnreadNotificationsCountQuery::class,
            Modules\Core\GraphQL\Queries\Country\CountriesListQuery::class,
            Modules\Core\GraphQL\Queries\User\GetUsersWithoutPaginateQuery::class,
    
            Modules\Core\GraphQL\Queries\FileVisibility\FileVisibilityWithoutPaginateQuery::class,
   
         
            Modules\Core\GraphQL\Queries\User\GetUserProfileQuery::class,
            // start of Bank
            'banks' => Modules\Core\GraphQL\Queries\Bank\BanksQuery::class,
            'searchBank' => Modules\Core\GraphQL\Queries\Bank\SearchBankQuery::class,
            // end of Bank
            // start of currency
            'currencies' => Modules\Core\GraphQL\Queries\Currency\CurrenciesQuery::class,
            'searchCurrencies' => Modules\Core\GraphQL\Queries\Currency\SearchCurrenciesQuery::class,
            // end of currency
            // start of cities
            Modules\Core\GraphQL\Queries\City\CitiesQuery::class,
            // end of cities
            // start of regions
            \Modules\Core\GraphQL\Queries\Region\GetRegionsQuery::class,
            // end of  regions
            // start of workspace
            Modules\Core\GraphQL\Queries\Workspace\GetWorkspacesQuery::class,
            Modules\Core\GraphQL\Queries\Workspace\GetWorkspaceByIdQuery::class,
            // end of workspace
            // start of invitation
            Modules\Core\GraphQL\Queries\Invitation\GetInvitationsQuery::class,
            // end of invitation

            // start of workspace user
            Modules\Core\GraphQL\Queries\WorkspaceUser\GetWorkspaceUsersQuery::class,
            Modules\Core\GraphQL\Queries\WorkspaceUser\GetAccessGrantsQuery::class,
            // end of workspace user
            
            // start of user group
            Modules\Core\GraphQL\Queries\UserGroup\GetUserGroupsQuery::class,
            // end of user group

        ],
        'mutation' => [
            Modules\Core\GraphQL\Mutations\Permission\CreateEditGroupMutation::class,

            // start of user
            Modules\Core\GraphQL\Mutations\User\SaveUserMutation::class,
            Modules\Core\GraphQL\Mutations\User\UpdateUserProfileMutation::class,
            Modules\Core\GraphQL\Mutations\User\SignoutMutation::class,
            Modules\Core\GraphQL\Mutations\User\ActiveDeactivateUserMutation::class,
            Modules\Core\GraphQL\Mutations\User\DeleteUserMutation::class,
            Modules\Core\GraphQL\Mutations\User\ChangeUserAdminNonAdminMutation::class,
            // end of user
   
            Modules\Core\GraphQL\Mutations\Translation\AddTranslationsMutation::class,
            Modules\Core\GraphQL\Mutations\Translation\UpdateTranslationsMutation::class,
            Modules\Core\GraphQL\Mutations\Translation\DeleteTranslationMutation::class,
            Modules\Core\GraphQL\Mutations\Translation\ImportTranslationDataMutation::class,
            Modules\Core\GraphQL\Mutations\Module\EditModuleMutation::class,
            Modules\Core\GraphQL\Mutations\Module\EnableDisableModuleMutation::class,
            Modules\Core\GraphQL\Mutations\Notification\MarkNotificationAsReadMutation::class,
            Modules\Core\GraphQL\Mutations\Notification\SendNotificationMutation::class,
            Modules\Core\GraphQL\Mutations\Notification\DeleteNotificationMutation::class,
            Modules\Core\GraphQL\Mutations\Notification\UpdateLastNotificationsClickMutation::class,
         
   
            // start of currency
            'createOrUpdateCurrency' => \Modules\Core\GraphQL\Mutations\Currency\CreateOrUpdateCurrencyMutation::class,
            'deleteCurrency' => \Modules\Core\GraphQL\Mutations\Currency\DeleteCurrencyMutation::class,
            // end of currency
            // start of bank
            'createOrUpdateBank' => \Modules\Core\GraphQL\Mutations\Bank\CreateOrUpdateBankMutation::class,
            'deleteBank' => \Modules\Core\GraphQL\Mutations\Bank\DeleteBankMutation::class,
            // end of bank
            // start of city
            Modules\Core\GraphQL\Mutations\City\CreateOrUpdateCityMutation::class,
            Modules\Core\GraphQL\Mutations\City\DeleteCityMutation::class,
            // end of city
            // start of regions
            Modules\Core\GraphQL\Mutations\Region\CreateOrUpdateRegionMutation::class,
            Modules\Core\GraphQL\Mutations\Region\DeleteRegionMutation::class,
            // end of regions
            // start of workspace
            Modules\Core\GraphQL\Mutations\Workspace\SaveWorkspaceMutation::class,
            Modules\Core\GraphQL\Mutations\Workspace\DeleteWorkspaceMutation::class,
            Modules\Core\GraphQL\Mutations\Workspace\SetDefaultWorkspaceMutation::class,
            // end of workspace
            // start of invitation
            Modules\Core\GraphQL\Mutations\Invitation\SendInvitationMutation::class,
            Modules\Core\GraphQL\Mutations\Invitation\DeleteInvitationMutation::class,
            Modules\Core\GraphQL\Mutations\Invitation\AcceptInvitationMutation::class,
            // end of invitation
            // start of workspace user
            Modules\Core\GraphQL\Mutations\WorkspaceUser\RevokeWorkspaceUserMutation::class,
            Modules\Core\GraphQL\Mutations\WorkspaceUser\RevokeAccessGrantMutation::class,
            Modules\Core\GraphQL\Mutations\WorkspaceUser\UpdateAccessGrantMutation::class,
            // end of workspace user
            Modules\Core\GraphQL\Mutations\User\FcmMutation::class,

            // start of user group
            Modules\Core\GraphQL\Mutations\UserGroup\SaveUserGroupMutation::class,
            Modules\Core\GraphQL\Mutations\UserGroup\DeleteUserGroupMutation::class,
            Modules\Core\GraphQL\Mutations\UserGroup\AcceptUserGroupInvitationMutation::class,
            Modules\Core\GraphQL\Mutations\UserGroup\SendUserGroupInvitationMutation::class,
            Modules\Core\GraphQL\Mutations\UserGroup\AssignUserGroupToScopeMutation::class,
            // end of user group
        ],
        'type' => [
            Modules\Core\GraphQL\Types\TranslatableInputType::class,
            Modules\Core\GraphQL\Types\TranslatableType::class,
            Modules\Core\GraphQL\Types\PagingType::class,
            Modules\Core\GraphQL\Types\Permission\PermissionsTreeType::class,
            Modules\Core\GraphQL\Types\Permission\GroupType::class,
            Modules\Core\GraphQL\Types\Permission\PermissionType::class,
            Modules\Core\GraphQL\Types\Permission\GroupResponseType::class,
            Modules\Core\GraphQL\Types\Permission\GroupsResponseType::class,
            Modules\Core\GraphQL\Types\GeneralResponse::class,
            Modules\Core\GraphQL\Types\FileDataType::class,
            Modules\Core\GraphQL\Types\AttachmentsUploadHistoryType::class,
            \Rebing\GraphQL\Support\UploadType::class,
            Modules\Core\GraphQL\Types\User\UsersResponseType::class,
            Modules\Core\GraphQL\Types\User\ModulesResponseType::class,
            Modules\Core\GraphQL\Types\User\UserType::class,
            Modules\Core\GraphQL\Types\Translation\PhraseInputType::class,
            Modules\Core\GraphQL\Types\Translation\TranslationsResponseType::class,
            Modules\Core\GraphQL\Types\Translation\TranslationType::class,
            Modules\Core\GraphQL\Types\Translation\TranslationTemplateDataType::class,
            Modules\Core\GraphQL\Types\Translation\TranslationTemplateResponseType::class,
            Modules\Core\GraphQL\Types\Translation\TranslationsUploadsResponseType::class,

            Modules\Core\GraphQL\Types\Module\ModulesResponseType::class,
            Modules\Core\GraphQL\Types\Module\ModuleType::class,
            Modules\Core\GraphQL\Types\Notification\NotificationResponseType::class,
            Modules\Core\GraphQL\Types\Notification\NotificationType::class,
            Modules\Core\GraphQL\Types\Country\CountryType::class,
            Modules\Core\GraphQL\Types\Country\CountryResponseType::class,
            Modules\Core\GraphQL\Types\TitleKeyType::class,

            Modules\Core\GraphQL\Types\FileVisibility\FileVisibilityType::class,
            Modules\Core\GraphQL\Types\FileVisibility\FileVisibilityResponseType::class,
            Modules\Core\GraphQL\Inputs\AttachmentInput::class,
            Modules\Core\GraphQL\Inputs\StampUserInput::class,
            Modules\Core\GraphQL\Types\User\SearchUsers::class,
            Modules\Core\GraphQL\Types\User\StampUserType::class,
            Modules\Core\GraphQL\Types\AttachmentByUrlType::class,
            Modules\Core\GraphQL\Types\AttachmentType::class,

            // start of Bank
            Modules\Core\GraphQL\Types\Bank\BankType::class,
            Modules\Core\GraphQL\Types\Bank\BanksResponseType::class,
            // start of currency
            Modules\Core\GraphQL\Types\Currency\CurrencyType::class,
            Modules\Core\GraphQL\Types\Currency\CurrenciesResponseType::class,
            // start of city
            Modules\Core\GraphQL\Types\City\CityType::class,
            Modules\Core\GraphQL\Types\City\CitiesResponseType::class,
            // end of city
            // start of  regions
            Modules\Core\GraphQL\Types\Region\RegionType::class,
            Modules\Core\GraphQL\Types\Region\RegionsResponseType::class,
            // end of regions
            // start of workspace
            Modules\Core\GraphQL\Types\Workspace\WorkspaceType::class,
            Modules\Core\GraphQL\Types\Workspace\WorkspacesResponseType::class,
            Modules\Core\GraphQL\Types\Workspace\WorkspaceSingleResponseType::class,
            // end of workspace
            // start of invitation
            Modules\Core\GraphQL\Types\Invitation\InvitationType::class,
            Modules\Core\GraphQL\Types\Invitation\InvitationsResponseType::class,
            Modules\Core\GraphQL\Types\Invitation\InvitationItemType::class,
            Modules\Core\GraphQL\Types\Invitation\InvitationSingleResponseType::class,
            Modules\Core\GraphQL\Inputs\InvitationItemInput::class,
            // end of invitation

            // start of workspace user
            Modules\Core\GraphQL\Types\WorkspaceUser\WorkspaceUserType::class,
            Modules\Core\GraphQL\Types\WorkspaceUser\WorkspaceUsersResponseType::class,
            Modules\Core\GraphQL\Types\WorkspaceUser\AccessGrantType::class,
            Modules\Core\GraphQL\Types\WorkspaceUser\AccessGrantsResponseType::class,
            Modules\Core\GraphQL\Types\Auth\LoginResponseType::class,

            // start of user group
            Modules\Core\GraphQL\Types\UserGroup\UserGroupType::class,
            Modules\Core\GraphQL\Types\UserGroup\UserGroupsResponseType::class,
            // end of user group

          
        ]
    ],
    'not_auth' => [
        'query' => [
    
        ],
        'mutation' => [
            Modules\Core\GraphQL\Mutations\Auth\RegisterMutation::class,
            Modules\Core\GraphQL\Mutations\Auth\LoginMutation::class,
            Modules\Core\GraphQL\Mutations\Auth\RequestPasswordResetMutation::class,
            Modules\Core\GraphQL\Mutations\Auth\CheckPasswordResetTokenMutation::class,
            Modules\Core\GraphQL\Mutations\Auth\ResetPasswordMutation::class,
        ],
        'type' => [
            Modules\Core\GraphQL\Types\Auth\LoginResponseType::class,
            Modules\Core\GraphQL\Types\Auth\RegisterResponseType::class,
            Modules\Core\GraphQL\Types\User\UserType::class,
            Modules\Core\GraphQL\Types\User\CheckPasswordResetTokenResponseType::class,
            Modules\Core\GraphQL\Types\Permission\GroupType::class,
            Modules\Core\GraphQL\Types\Permission\PermissionType::class,
            Modules\Core\GraphQL\Types\TranslatableType::class,
            Modules\Core\GraphQL\Types\GeneralResponse::class,
            \Rebing\GraphQL\Support\UploadType::class,
            Modules\Core\GraphQL\Types\FileDataType::class,
            Modules\Core\GraphQL\Types\Translation\TranslationFileٍResponseType::class,
            Modules\Core\GraphQL\Types\TranslatableInputType::class,
            Modules\Core\GraphQL\Types\TranslatableType::class,
            Modules\Core\GraphQL\Types\PagingType::class,
            Modules\Core\GraphQL\Types\Workspace\WorkspaceType::class,
        ]
    ]
];
