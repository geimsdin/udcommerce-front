<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Unusualdope\LaravelEcommerce\Models\SocialAccount;
use Unusualdope\LaravelEcommerce\Models\SocialAuthProvider;

class SocialAuthController extends Controller
{
    /**
     * Redirect to the social provider's OAuth page.
     */
    public function redirect(string $provider): RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($this->getDriverName($provider))->redirect();
    }

    /**
     * Handle the callback from the social provider.
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            /** @var \Laravel\Socialite\Two\User $socialUser */
            $socialUser = Socialite::driver($this->getDriverName($provider))->user();
        } catch (\Exception $e) {
            Log::error("Social auth callback error [{$provider}]: ".$e->getMessage());

            return redirect('/login')->with('error', __('front-ecommerce::social-auth.callback_error'));
        }

        // Find existing social account
        $socialAccount = SocialAccount::findByProvider($provider, $socialUser->getId());

        if ($socialAccount) {
            // Update token data
            $socialAccount->update([
                'token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'token_expires_at' => $socialUser->expiresIn
                    ? now()->addSeconds($socialUser->expiresIn)
                    : null,
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
            ]);

            Auth::login($socialAccount->user, remember: true);

            return redirect()->intended('/account');
        }

        // Check if a user with this email already exists
        $user = null;
        $email = $socialUser->getEmail();

        if ($email) {
            $user = User::where('email', $email)->first();
        }

        // Create a new user if none found
        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email' => $email ?? $provider.'_'.$socialUser->getId().'@social.local',
                'password' => bcrypt(Str::random(24)),
            ]);
        }

        // Link social account to user
        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'name' => $socialUser->getName(),
            'email' => $email,
            'avatar' => $socialUser->getAvatar(),
            'token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
            'token_expires_at' => $socialUser->expiresIn
                ? now()->addSeconds($socialUser->expiresIn)
                : null,
        ]);

        Auth::login($user, remember: true);

        return redirect()->intended('/account');
    }

    /**
     * Validate that the provider is supported and active.
     */
    protected function validateProvider(string $provider): void
    {
        if (! array_key_exists($provider, SocialAuthProvider::PROVIDERS)) {
            abort(404, 'Provider not supported.');
        }

        $config = SocialAuthProvider::getByProvider($provider);

        if (! $config || ! $config->isConfigured()) {
            abort(404, 'Provider not configured.');
        }
    }

    /**
     * Get the Socialite driver name for the given provider.
     */
    protected function getDriverName(string $provider): string
    {
        return $provider === 'twitter' ? 'twitter-oauth-2' : $provider;
    }
}
