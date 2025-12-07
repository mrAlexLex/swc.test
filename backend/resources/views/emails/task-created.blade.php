<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Task Created</title>
</head>
<body
    style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
<div
    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px 10px 0 0;">
    <h1 style="color: #fff; margin: 0; font-size: 24px;">New Task Created</h1>
</div>
<div
    style="background: #fff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 10px 10px;">
    <p style="margin-top: 0;">Hello, <strong>{{ $task->user->name }}</strong>!</p>
    <p>A new task has been created:</p>
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="margin: 0 0 15px 0; color: #333; font-size: 18px;">{{ $task->title }}</h2>
        <p style="margin: 0 0 10px 0; color: #666;">{{ $task->description }}</p>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                <span
                    style="display: inline-block; background: #667eea; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 12px;">
                    {{ $task->status->label() }}
                </span>
            @if($task->completion_date)
                <span style="display: inline-block; color: #666; font-size: 12px; margin-left: 10px;">
                        Due: {{ $task->completion_date->format('M d, Y') }}
                    </span>
            @endif
        </div>
    </div>
    <p style="color: #666; font-size: 14px; margin-bottom: 0;">
        This is an automated notification from the Task Management System.
    </p>
</div>
<div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
    &copy; {{ date('Y') }} Task Management API. All rights reserved.
</div>
</body>
</html>

