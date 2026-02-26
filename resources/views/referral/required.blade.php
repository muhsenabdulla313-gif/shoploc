                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const referralCodeInput = document.getElementById('referral_code');
            if (referralCodeInput && referralCodeInput.value) {
                localStorage.setItem('referral_code', referralCodeInput.value);
            }
        });
    }
});
</script>
@endpush