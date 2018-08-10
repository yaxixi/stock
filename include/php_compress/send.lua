-- send.lua
-- Created by yaxixi，2011.08.05
-- 用来保存命令、消息宏

CMD_GET_LOGIN_DIST                 = 0xAA01;
MSG_GET_LOGIN_DIST                 = 0xAA02;
CMD_AUTH_ACCOUNT                   = 0xAA03;
MSG_AUTH_ACCOUNT_RESULT            = 0xAA04;
CMD_GET_MAP_RANK_LIST              = 0xAA05;
MSG_GET_MAP_RANK_LIST              = 0xAA06;
CMD_GET_LML_PAGE                   = 0xAA07;
MSG_GET_LML_PAGE                   = 0xAA08;
CMD_SET_NICK_NAME                  = 0xAA09;
MSG_NICK_NAME_RET                  = 0xAA0A;
CMD_SYNC_SESSION_KEY               = 0xAA0B;
MSG_SYNC_SESSION_KEY_OK            = 0xAA0C;
MSG_DIALOG_OK                      = 0xAA0D;
CMD_TAKE_OVER_ACCOUNT              = 0xAA0E;

CMD_INTERNAL_AUTH                  = 0xAAC0;
CMD_SHUTDOWN                       = 0xAAC1;
MSG_REDIS_COMPRESS_RESULT          = 0xAAFA;
CMD_FIND_ROOM_IN_MASTER            = 0xAAFB;
MSG_FIND_ROOM_RESULT               = 0xAAFC;
CMD_REDIS_SELECT                   = 0xAAFD;
CMD_REDIS_BATCH_SELECT             = 0xAAFE;
MSG_REDIS_RESULT                   = 0xAAFF;

CMD_REMOTE_CALL_MSG                = 0xC0E1;
MSG_REMOTE_CALL                    = 0xC0E2;
CMD_REMOTE_POST_MSG                = 0xC0E3;
CMD_REMOTE_POST                    = 0xC0E4;
CMD_REMOTE_CALL_CRT                = 0xC0E5;
CMD_TEST                           = 0xC0E6;
MSG_TEST                           = 0xC0E7;
CMD_GM_COMMAND                     = 0xC0E8;
MSG_GM_COMMAND_RET                 = 0xC0E9;

MSG_DB_RESULT                      = 0xEE01;
CMD_ECHO                           = 0xFF01;
MSG_REPLY_ECHO                     = 0xFF02;
MSG_NOTIFY_FROM_CPP                = 0xFFF0;
