array (
  'config' => 
  array (
    'show_timestamps' => true,
    'enable_channels' => true,
    'log_path' => '{path.log}/logger-{env.APP_MODE}',
  ),
  'defaults' => 
  array (
    'handler_class' => 'Monolog\\Formatter\\LineFormatter',
    'formatter_class' => 'Monolog\\Handler\\StreamHandler',
    'formatter_format' => '[%timestamp%] %channel%.%level_name%: %message% %context%',
  ),
  'logger' => 
  array (
    'handlers' => 
    array (
      0 => 'print_writer',
    ),
    'processors' => 
    array (
      0 => 'timestamp',
    ),
    'filters' => 
    array (
    ),
  ),
  'handlers' => 
  array (
    'buffer_writer' => 
    array (
      'class' => 'Library\\Logger\\Writers\\BufferWriter',
      'handlers' => 
      array (
        0 => 'print_writer',
      ),
      'processors' => 
      array (
      ),
      'extra_opt' => '/tmp/log.txt',
    ),
    'print_writer' => 
    array (
      'class' => 'Library\\Logger\\Writers\\PrintWriter',
      'processors' => 
      array (
      ),
      'handlers' => 
      array (
      ),
    ),
  ),
  'formatters' => 
  array (
    'passthru' => 
    array (
      'class' => 'Library\\Logger\\Formatters\\PassthruFormatter',
      'format' => '[%timestamp%] %channel%.%level_name%: %message% %context%',
    ),
    'line' => 
    array (
      'class' => 'Library\\Logger\\Formatters\\LineFormatter',
    ),
  ),
  'processors' => 
  array (
    'tag' => 
    array (
      'class' => 'Library\\Logger\\Processors\\TagProcessor',
    ),
    'hostname' => 
    array (
      'class' => 'Library\\Logger\\Processors\\HostnameProcessor',
    ),
    'timestamp' => 
    array (
      'class' => 'Library\\Logger\\Processors\\TimestampProcessor',
    ),
  ),
  'filters' => 
  array (
    'tag' => 
    array (
      'class' => 'Library\\Logger\\Filters\\TagFilter',
    ),
    'level' => 
    array (
      'class' => 'Library\\Logger\\Filters\\LevelFilter',
    ),
    'channel' => 
    array (
      'class' => 'Library\\Logger\\Filters\\ChannelFilter',
    ),
  ),
  'writers' => 
  array (
    'name' => 
    array (
      'class' => NULL,
      'formatter' => NULL,
      'handlers' => 
      array (
      ),
      'processors' => 
      array (
      ),
    ),
  ),
  'cascade' => 
  array (
    'name' => 
    array (
      'class' => NULL,
    ),
  ),
)