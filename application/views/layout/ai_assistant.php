<!-- Gemini AI Assistant Drawer -->
<div id="aiAssistantDrawer" class="ai-drawer">
    <div class="ai-drawer-header">
        <div class="ai-drawer-title">
            <i class="fas fa-robot text-primary"></i>
            <span>Gemini AI Assistant</span>
            <span class="badge badge-primary" style="font-size:10px; margin-left:6px; background:#2563eb;">DB Integrated</span>
        </div>
        <div class="ai-drawer-actions">
            <button type="button" class="btn btn-default btn-xs" id="btnAiSettings" title="Gemini API Key Settings">
                <i class="fas fa-cog"></i>
            </button>
            <button type="button" class="btn btn-default btn-xs" id="closeAiDrawer" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="ai-drawer-body" id="aiChatContainer">
        <div class="ai-msg ai-msg-system">
            <div class="ai-avatar"><i class="fas fa-robot"></i></div>
            <div class="ai-bubble">
                Hello! I am your Database-Integrated AI Assistant powered by Google Gemini. 🤖<br><br>
                You can ask me to perform data entry (e.g. <b>"Add a new student John Doe in class 1"</b>, <b>"Add expense 500 for electricity bill"</b>, <b>"Add fee type Sports Fee"</b>) or ask queries about your school database!
            </div>
        </div>
    </div>

    <div class="ai-quick-prompts">
        <span class="ai-chip" data-prompt="Add a new student named Amit Sharma in Class 1 Section A">➕ Add Student</span>
        <span class="ai-chip" data-prompt="Add an expense of 1500 for Office Supplies today">💸 Add Expense</span>
        <span class="ai-chip" data-prompt="Add a new fee type called Annual Sports Fee">🏷️ Add Fee Type</span>
        <span class="ai-chip" data-prompt="What is today's total fee collection?">📊 Today Collection</span>
    </div>

    <div class="ai-drawer-footer">
        <div class="input-group">
            <input type="text" id="aiPromptInput" class="form-control" placeholder="Type your data entry or query prompt..." autocomplete="off">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="btnSendAiPrompt" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </span>
        </div>
    </div>
</div>

<!-- Gemini API Key Modal -->
<div class="modal fade" id="aiKeyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fas fa-key text-primary me-2"></i> Configure Google Gemini API Key</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Google Gemini API Key <span class="text-danger">*</span></label>
                    <input type="password" id="inputGeminiApiKey" class="form-control" placeholder="AIzaSy..." value="">
                    <span class="help-block text-muted">Get your free API Key from <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveGeminiKey">Save Key</button>
            </div>
        </div>
    </div>
</div>

<style>
.ai-drawer {
    position: fixed;
    top: 60px;
    right: -420px;
    width: 400px;
    height: calc(100vh - 60px);
    background: #ffffff;
    border-left: 1px solid #e2e8f0;
    box-shadow: -4px 0 25px rgba(15, 23, 42, 0.12);
    z-index: 99999;
    display: flex;
    flex-direction: column;
    transition: right 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
}
.ai-drawer.open {
    right: 0;
}
.ai-drawer-header {
    padding: 14px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ai-drawer-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ai-drawer-actions {
    display: flex;
    gap: 6px;
}
.ai-drawer-body {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.ai-msg {
    display: flex;
    gap: 10px;
    max-width: 88%;
}
.ai-msg-system {
    align-self: flex-start;
}
.ai-msg-user {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.ai-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.ai-msg-user .ai-avatar {
    background: #0f172a;
    color: #ffffff;
}
.ai-bubble {
    padding: 10px 14px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
}
.ai-msg-system .ai-bubble {
    background: #f1f5f9;
    color: #1e293b;
    border-top-left-radius: 2px;
}
.ai-msg-user .ai-bubble {
    background: #2563eb;
    color: #ffffff;
    border-top-right-radius: 2px;
}
.ai-action-badge {
    margin-top: 8px;
    padding: 8px 12px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #16a34a;
    border-radius: 8px;
    font-weight: 600;
    font-size: 12px;
}
.ai-quick-prompts {
    padding: 8px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 6px;
    overflow-x: auto;
    white-space: nowrap;
}
.ai-chip {
    display: inline-block;
    padding: 4px 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
}
.ai-chip:hover {
    background: #eff6ff;
    border-color: #2563eb;
    color: #2563eb;
}
.ai-drawer-footer {
    padding: 12px 16px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}
@media (max-width: 480px) {
    .ai-drawer {
        width: 100vw;
        right: -100vw;
    }
}
</style>

<script type="text/javascript">
$(document).ready(function() {
    // Open/Close AI Drawer
    $(document).on('click', '#btn-ai-assistant', function(e) {
        e.preventDefault();
        $('#aiAssistantDrawer').toggleClass('open');
        checkGeminiKeyStatus();
    });

    $(document).on('click', '#closeAiDrawer', function() {
        $('#aiAssistantDrawer').removeClass('open');
    });

    $(document).on('click', '#btnAiSettings', function() {
        $.ajax({
            url: base_url + 'ai_assistant/get_key',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res && res.key) {
                    $('#inputGeminiApiKey').val(res.key);
                }
                $('#aiKeyModal').modal('show');
            }
        });
    });

    $(document).on('click', '#btnSaveGeminiKey', function() {
        var key = $('#inputGeminiApiKey').val().trim();
        if (!key) {
            alert('Please enter a valid Gemini API Key.');
            return;
        }
        $.ajax({
            url: base_url + 'ai_assistant/save_key',
            type: 'POST',
            data: { gemini_api_key: key },
            dataType: 'json',
            success: function(res) {
                $('#aiKeyModal').modal('hide');
                alert(res.message);
            }
        });
    });

    $(document).on('click', '.ai-chip', function() {
        var prompt = $(this).data('prompt');
        $('#aiPromptInput').val(prompt);
        sendAiPrompt();
    });

    $(document).on('click', '#btnSendAiPrompt', function() {
        sendAiPrompt();
    });

    $('#aiPromptInput').on('keypress', function(e) {
        if (e.which == 13) {
            sendAiPrompt();
        }
    });

    function checkGeminiKeyStatus() {
        $.ajax({
            url: base_url + 'ai_assistant/get_key',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res || !res.key) {
                    $('#aiKeyModal').modal('show');
                }
            }
        });
    }

    function sendAiPrompt() {
        var prompt = $('#aiPromptInput').val().trim();
        if (!prompt) return;

        // Append User Message
        appendMessage('user', prompt);
        $('#aiPromptInput').val('');

        // Loading indicator
        var loadingId = 'loading_' + Date.now();
        appendMessage('system', '<i class="fas fa-spinner fa-spin me-1"></i> Processing request...', loadingId);

        $.ajax({
            url: base_url + 'ai_assistant/process',
            type: 'POST',
            data: { prompt: prompt },
            dataType: 'json',
            success: function(res) {
                $('#' + loadingId).remove();
                if (res.status === 'success') {
                    var cleanText = res.response.replace(/```json[\s\S]*?```/g, '').trim();
                    var html = cleanText.replace(/\n/g, '<br>');

                    if (res.action_result && res.action_result.message) {
                        html += '<div class="ai-action-badge"><i class="fas fa-check-circle me-1"></i> ' + res.action_result.message + '</div>';
                    }
                    appendMessage('system', html);
                } else if (res.status === 'need_key') {
                    appendMessage('system', '⚠️ ' + res.message);
                    $('#aiKeyModal').modal('show');
                } else {
                    appendMessage('system', '❌ ' + (res.message || 'An error occurred.'));
                }
            },
            error: function() {
                $('#' + loadingId).remove();
                appendMessage('system', '❌ Failed to communicate with server.');
            }
        });
    }

    function appendMessage(sender, text, msgId) {
        var idAttr = msgId ? ' id="' + msgId + '"' : '';
        var icon = sender === 'user' ? 'fa-user' : 'fa-robot';
        var html = '<div class="ai-msg ai-msg-' + sender + '"' + idAttr + '>' +
                        '<div class="ai-avatar"><i class="fas ' + icon + '"></i></div>' +
                        '<div class="ai-bubble">' + text + '</div>' +
                   '</div>';
        $('#aiChatContainer').append(html);
        $('#aiChatContainer').scrollTop($('#aiChatContainer')[0].scrollHeight);
    }
});
</script>
