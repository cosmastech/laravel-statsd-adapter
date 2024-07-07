<?php

return [
    'default' => env("STATSD_ADAPTER_DEFAULT", "memory"),

    /**
     * You may name your channel anything you wish. Valid drivers are:
     *      memory
     *      league
     *      datadog
     *      log_datadog
     */
    "channels" => [
        "memory" => [
            "adapter" => "memory",
        ],
        "league" => [
            "adapter" => "league",
            "instance_id" => null,
            'host' => env('STATSD_HOST', '127.0.0.1'),
            'port' => env('STATSD_PORT', 8125),
            'namespace' => env('STATSD_NAMESPACE', ''),
            'throwConnectionExceptions' => true,
        ],
        "datadog" => [
            "adapter" => "datadog",
            "host" => env("DD_AGENT_HOST"),
            "port" => env("DD_DOGSTATSD_PORT"),
            "socket_path" => null,
            "datadog_host" => null,
            "decimal_precision" => null,
            "global_tags" => [],
            "metric_prefix" => null,
            "disable_telemetry" => null,
        ],
        "log_datadog" => [
            "adapter" => "log_datadog",
            "log_level" => "debug",
            "decimal_precision" => null,
            "global_tags" => [],
            "metric_prefix" => null,
            "disable_telemetry" => null,
        ],
    ],
];
