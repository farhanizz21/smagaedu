<div id="chatWidget" class="fixed bottom-5 right-5 z-[60]">
    <section id="chatWidgetPanel"
        class="hidden mb-3 h-[min(620px,calc(100vh-110px))] w-[min(380px,calc(100vw-24px))] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl"
        aria-label="Chat personal">
        <header class="flex h-16 shrink-0 items-center justify-between bg-blue-600 px-4 text-white">
            <div class="flex min-w-0 items-center gap-3">
                <button id="chatBackButton" type="button"
                    class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-full hover:bg-blue-500"
                    aria-label="Kembali ke daftar chat">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </button>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/15">
                    <i data-lucide="messages-square" class="h-5 w-5"></i>
                </span>
                <div class="min-w-0">
                    <h2 id="chatPanelTitle" class="truncate text-sm font-bold">Chat Personal</h2>
                    <p id="chatPanelSubtitle" class="text-xs text-blue-100">Pilih akun untuk memulai chat</p>
                </div>
            </div>
            <button id="chatCloseButton" type="button"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full hover:bg-blue-500"
                aria-label="Tutup chat">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </header>

        <div id="chatContactsView" class="flex min-h-0 flex-1 flex-col">
            <div class="border-b border-gray-100 p-3">
                <label for="chatContactSearch" class="sr-only">Cari akun</label>
                <input id="chatContactSearch" type="search" placeholder="Cari nama atau role..."
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div id="chatContactList" class="min-h-0 flex-1 overflow-y-auto">
                <p id="chatContactStatus" class="px-4 py-8 text-center text-sm text-gray-500">Memuat daftar akun...</p>
            </div>
        </div>

        <div id="chatConversationView" class="hidden min-h-0 flex-1 flex-col">
            <div id="chatMessages" class="flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto bg-gray-50 p-4"
                aria-live="polite"></div>
            <p id="chatError" class="hidden px-4 pt-2 text-xs text-red-600"></p>
            <form id="chatSendForm" class="flex shrink-0 items-end gap-2 border-t border-gray-100 p-3">
                <input id="chatRecipient" type="hidden" name="recipient_uuid">
                <label for="chatMessageInput" class="sr-only">Tulis pesan</label>
                <textarea id="chatMessageInput" name="message" rows="1" maxlength="2000" required
                    placeholder="Tulis pesan..."
                    class="max-h-28 min-h-11 flex-1 resize-y rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                <button id="chatSendButton" type="submit"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    aria-label="Kirim pesan">
                    <i data-lucide="send" class="h-4 w-4"></i>
                </button>
            </form>
        </div>
    </section>

    <button id="chatLauncher" type="button"
        class="ml-auto flex h-14 items-center gap-3 rounded-full bg-blue-600 px-5 text-sm font-bold text-white shadow-xl transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200"
        aria-expanded="false" aria-controls="chatWidgetPanel">
        <span class="relative flex h-7 w-7 items-center justify-center">
            <i data-lucide="bell" class="h-6 w-6"></i>
            <span id="chatUnreadBadge"
                class="<?= $unread_chat_count > 0 ? 'flex' : 'hidden'; ?> absolute -right-2 -top-2 min-w-5 h-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white"><?= $unread_chat_count > 99 ? '99+' : (int) $unread_chat_count; ?></span>
        </span>
        <span>Chat</span>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var launcher = document.getElementById('chatLauncher');
    var panel = document.getElementById('chatWidgetPanel');
    var closeButton = document.getElementById('chatCloseButton');
    var backButton = document.getElementById('chatBackButton');
    var contactsView = document.getElementById('chatContactsView');
    var conversationView = document.getElementById('chatConversationView');
    var contactsList = document.getElementById('chatContactList');
    var contactsStatus = document.getElementById('chatContactStatus');
    var contactSearch = document.getElementById('chatContactSearch');
    var messagesList = document.getElementById('chatMessages');
    var sendForm = document.getElementById('chatSendForm');
    var messageInput = document.getElementById('chatMessageInput');
    var recipientInput = document.getElementById('chatRecipient');
    var sendButton = document.getElementById('chatSendButton');
    var errorMessage = document.getElementById('chatError');
    var panelTitle = document.getElementById('chatPanelTitle');
    var panelSubtitle = document.getElementById('chatPanelSubtitle');
    var unreadBadge = document.getElementById('chatUnreadBadge');
    var contactsUrl = <?= json_encode(base_url('chat/contacts')); ?>;
    var unreadUrl = <?= json_encode(base_url('chat/unread_count')); ?>;
    var messagesUrlBase = <?= json_encode(base_url('chat/messages/')); ?>;
    var sendUrl = <?= json_encode(base_url('chat/send')); ?>;
    var contacts = [];
    var activeContact = null;
    var lastMessageId = 0;
    var renderedMessageIds = {};
    var panelOpen = false;
    var refreshTimer = null;
    var contactsTimer = null;

    function setUnreadCount(count) {
        count = Number(count) || 0;
        unreadBadge.textContent = count > 99 ? '99+' : String(count);
        unreadBadge.classList.toggle('hidden', count === 0);
        unreadBadge.classList.toggle('flex', count > 0);
        unreadBadge.setAttribute('aria-label', count + ' pesan belum dibaca');
    }

    function requestJson(url, options) {
        return fetch(url, Object.assign({cache: 'no-store'}, options || {})).then(function(response) {
            return response.json().catch(function() {
                throw new Error('Respons server tidak dapat dibaca.');
            }).then(function(data) {
                if (!response.ok) throw new Error(data.error || 'Permintaan chat gagal.');
                return data;
            });
        });
    }

    function refreshUnreadCount() {
        requestJson(unreadUrl).then(function(data) {
            setUnreadCount(data.count);
        }).catch(function(error) {
            console.error(error);
        });
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
    }

    function formatTime(value) {
        var date = new Date(String(value).replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) return value;
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function renderContacts() {
        contactsList.querySelectorAll('.chat-contact-item').forEach(function(item) {
            item.remove();
        });

        var query = contactSearch.value.trim().toLowerCase();
        var visibleContacts = contacts.filter(function(contact) {
            return (contact.nama + ' ' + contact.role_nama).toLowerCase().indexOf(query) !== -1;
        });

        contactsStatus.classList.toggle('hidden', visibleContacts.length > 0);
        contactsStatus.textContent = contacts.length === 0 ? 'Belum ada akun lain yang dapat dihubungi.' :
            (visibleContacts.length === 0 ? 'Akun tidak ditemukan.' : '');

        visibleContacts.forEach(function(contact) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'chat-contact-item flex w-full items-center gap-3 border-b border-gray-100 px-4 py-3 text-left hover:bg-blue-50';
            button.addEventListener('click', function() {
                openConversation(contact);
            });

            var avatar = document.createElement('span');
            avatar.className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700';
            avatar.textContent = contact.nama.charAt(0).toUpperCase();
            var details = document.createElement('span');
            details.className = 'min-w-0 flex-1';
            var name = document.createElement('span');
            name.className = 'block truncate text-sm font-semibold text-gray-800';
            name.textContent = contact.nama;
            var preview = document.createElement('span');
            preview.className = 'block truncate text-xs text-gray-500';
            preview.textContent = contact.latest_message || contact.role_nama.replace(/_/g, ' ');
            details.appendChild(name);
            details.appendChild(preview);
            button.appendChild(avatar);
            button.appendChild(details);

            if (Number(contact.unread_count) > 0) {
                var unread = document.createElement('span');
                unread.className = 'flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white';
                unread.textContent = Number(contact.unread_count) > 99 ? '99+' : contact.unread_count;
                button.appendChild(unread);
            }
            contactsList.appendChild(button);
        });
    }

    function loadContacts() {
        return requestJson(contactsUrl).then(function(data) {
            contacts = data.contacts || [];
            renderContacts();
        }).catch(function(error) {
            contactsStatus.textContent = error.message;
            contactsStatus.classList.remove('hidden');
        });
    }

    function renderMessage(message) {
        if (renderedMessageIds[message.id]) return;
        renderedMessageIds[message.id] = true;
        var row = document.createElement('div');
        row.className = 'flex ' + (message.sender_uuid === <?= json_encode($this->session->userdata('uuid')); ?> ?
            'justify-end' : 'justify-start');
        var bubble = document.createElement('div');
        bubble.className = 'max-w-[85%] rounded-2xl px-3 py-2 ' +
            (message.sender_uuid === <?= json_encode($this->session->userdata('uuid')); ?> ?
                'bg-blue-600 text-white' : 'border border-gray-200 bg-white text-gray-800');
        var text = document.createElement('p');
        text.className = 'whitespace-pre-wrap break-words text-sm';
        text.textContent = message.message;
        var time = document.createElement('time');
        time.className = 'mt-1 block text-right text-[10px] ' +
            (message.sender_uuid === <?= json_encode($this->session->userdata('uuid')); ?> ?
                'text-blue-100' : 'text-gray-400');
        time.dateTime = message.created_at;
        time.textContent = formatTime(message.created_at);
        bubble.appendChild(text);
        bubble.appendChild(time);
        row.appendChild(bubble);
        messagesList.appendChild(row);
        lastMessageId = Math.max(lastMessageId, Number(message.id));
    }

    function loadMessages(afterId) {
        var contactUuid = activeContact.uuid;
        var url = messagesUrlBase + encodeURIComponent(contactUuid);
        if (afterId !== null) url += '?after=' + encodeURIComponent(afterId);
        return requestJson(url, {headers: {'Accept': 'application/json'}}).then(function(data) {
            if (!activeContact || activeContact.uuid !== contactUuid) return null;
            data.messages.forEach(renderMessage);
            if (data.messages.length) messagesList.scrollTop = messagesList.scrollHeight;
            refreshUnreadCount();
            loadContacts();
            return data;
        });
    }

    function openConversation(contact) {
        activeContact = contact;
        lastMessageId = 0;
        renderedMessageIds = {};
        messagesList.textContent = '';
        errorMessage.classList.add('hidden');
        recipientInput.value = contact.uuid;
        panelTitle.textContent = contact.nama;
        panelSubtitle.textContent = contact.role_nama.replace(/_/g, ' ');
        contactsView.classList.add('hidden');
        conversationView.classList.remove('hidden');
        conversationView.classList.add('flex');
        backButton.classList.remove('hidden');

        loadMessages(null).then(function(data) {
            if (data && data.messages.length) messagesList.scrollTop = messagesList.scrollHeight;
        }).catch(function(error) {
            showError(error.message);
        });
    }

    function showContacts() {
        activeContact = null;
        contactsView.classList.remove('hidden');
        conversationView.classList.add('hidden');
        conversationView.classList.remove('flex');
        backButton.classList.add('hidden');
        panelTitle.textContent = 'Chat Personal';
        panelSubtitle.textContent = 'Pilih akun untuk memulai chat';
        loadContacts();
    }

    function openPanel() {
        panelOpen = true;
        panel.classList.remove('hidden');
        panel.classList.add('flex');
        launcher.setAttribute('aria-expanded', 'true');
        loadContacts();
        refreshUnreadCount();
        contactsTimer = window.setInterval(loadContacts, 20000);
        refreshTimer = window.setInterval(function() {
            if (activeContact) {
                loadMessages(lastMessageId).catch(function(error) {
                    showError(error.message);
                });
            } else {
                refreshUnreadCount();
            }
        }, 4000);
    }

    function closePanel() {
        panelOpen = false;
        panel.classList.add('hidden');
        panel.classList.remove('flex');
        launcher.setAttribute('aria-expanded', 'false');
        window.clearInterval(refreshTimer);
        window.clearInterval(contactsTimer);
    }

    launcher.addEventListener('click', function() {
        if (panelOpen) closePanel();
        else openPanel();
    });
    closeButton.addEventListener('click', closePanel);
    backButton.addEventListener('click', showContacts);
    contactSearch.addEventListener('input', renderContacts);

    sendForm.addEventListener('submit', function(event) {
        event.preventDefault();
        if (!activeContact || !messageInput.value.trim()) return;
        errorMessage.classList.add('hidden');
        sendButton.disabled = true;
        var formData = new FormData(sendForm);
        requestJson(sendUrl, {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
        }).then(function(data) {
            renderMessage(data.message);
            messagesList.scrollTop = messagesList.scrollHeight;
            messageInput.value = '';
            loadContacts();
        }).catch(function(error) {
            showError(error.message);
        }).finally(function() {
            sendButton.disabled = false;
            messageInput.focus();
        });
    });

    window.setInterval(refreshUnreadCount, 15000);
    if (window.lucide) lucide.createIcons();
});
</script>
