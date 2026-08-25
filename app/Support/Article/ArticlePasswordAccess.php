<?php

namespace App\Support\Article;

use App\Models\Article\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class ArticlePasswordAccess
{
    public function hashForStorage(string $password): string
    {
        return Hash::make($password);
    }

    public function allows(Article $article, Request $request): bool
    {
        $stored = (string) $article->password_protected;
        $granted = (string) $request->session()->get($this->sessionKey($article), '');

        return $stored !== '' && $granted !== '' && hash_equals($this->fingerprint($stored), $granted);
    }

    public function attempt(Article $article, string $password, Request $request): bool
    {
        $stored = (string) $article->password_protected;
        if ($stored === '' || $password === '') {
            return false;
        }

        $isHash = (password_get_info($stored)['algo'] ?? null) !== null;
        $matches = $isHash ? Hash::check($password, $stored) : hash_equals($stored, $password);
        if (! $matches) {
            return false;
        }

        if (! $isHash) {
            $stored = $this->hashForStorage($password);
            $article->forceFill(['password_protected' => $stored])->save();
        }

        $request->session()->put($this->sessionKey($article), $this->fingerprint($stored));

        return true;
    }

    private function sessionKey(Article $article): string
    {
        return 'article_password_access.'.$article->getKey();
    }

    private function fingerprint(string $stored): string
    {
        return hash_hmac('sha256', $stored, (string) config('app.key'));
    }
}
