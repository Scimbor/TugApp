<div class="space-y-4">
    @if(!$token)
        <p class="text-sm text-gray-500 dark:text-gray-400">Brak wygenerowanego tokenu.</p>
    @else
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 space-y-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-sm">{{ $token->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Utworzono: {{ $token->created_at->format('Y-m-d H:i:s') }}
                        @if($token->last_used_at)
                            | Ostatnie użycie: {{ $token->last_used_at->format('Y-m-d H:i:s') }}
                        @else
                            | Ostatnie użycie: Nigdy
                        @endif
                    </p>
                </div>
                <button 
                    type="button"
                    onclick="deleteToken({{ $token->id }})"
                    class="text-danger-600 hover:text-danger-700 dark:text-danger-400 dark:hover:text-danger-300 text-sm font-medium"
                >
                    Usuń
                </button>
            </div>
            <div class="flex items-center gap-2">
                <input 
                    type="text" 
                    value="{{ $token->token }}" 
                    readonly 
                    class="flex-1 text-xs font-mono bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded px-2 py-1"
                    id="token-{{ $token->id }}"
                >
                <button 
                    type="button"
                    onclick="copyToken('token-{{ $token->id }}')"
                    class="px-3 py-1 text-xs bg-primary-600 text-white rounded hover:bg-primary-700"
                >
                    Kopiuj
                </button>
            </div>
        </div>
    @endif
</div>

<script>
function copyToken(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const button = input.nextElementSibling;
        const originalText = button.textContent;
        button.textContent = 'Skopiowano!';
        button.classList.add('bg-success-600');
        button.classList.remove('bg-primary-600', 'hover:bg-primary-700');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-success-600');
            button.classList.add('bg-primary-600', 'hover:bg-primary-700');
        }, 2000);
    });
}

function deleteToken(tokenId) {
    if (!confirm('Czy na pewno chcesz usunąć ten token?')) {
        return;
    }
    
    @if($userId)
        @this.call('deleteToken', tokenId);
    @endif
}
</script>
