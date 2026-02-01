<?php

namespace App\Http\Controllers;

/**
 * 共通コントローラー。
 *
 * フラッシュメッセージの形式を <x-flash /> に合わせて統一する。
 */
abstract class Controller
{
    /**
     * ルート名へ遷移しつつフラッシュを付与する。
     *
     * @param mixed $parameters route() に渡すパラメータ（配列/モデル可）
     */
    protected function redirectRouteWithFlash(string $route, mixed $parameters = [], array $flash = []): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route($route, $parameters)->with('flash', $flash);
    }

    /**
     * URLへ遷移しつつフラッシュを付与する。
     */
    protected function redirectWithFlash(string $to, array $flash): \Illuminate\Http\RedirectResponse
    {
        return redirect()->to($to)->with('flash', $flash);
    }

    /**
     * ルート名へ遷移しつつ成功フラッシュを付与する。
     *
     * @param mixed $parameters route() に渡すパラメータ（配列/モデル可）
     */
    protected function redirectRouteWithSuccess(string $route, mixed $parameters, string $message): \Illuminate\Http\RedirectResponse
    {
        return $this->redirectRouteWithFlash($route, $parameters, $this->flashSuccess($message));
    }

    /**
     * ルート名へ遷移しつつエラーフラッシュを付与する。
     *
     * @param mixed $parameters route() に渡すパラメータ（配列/モデル可）
     */
    protected function redirectRouteWithError(string $route, mixed $parameters, string $message): \Illuminate\Http\RedirectResponse
    {
        return $this->redirectRouteWithFlash($route, $parameters, $this->flashError($message));
    }

    protected function flash(string $type, string $message): array
    {
        return ['type' => $type, 'message' => $message];
    }

    protected function flashSuccess(string $message): array
    {
        return $this->flash('success', $message);
    }

    protected function flashError(string $message): array
    {
        return $this->flash('error', $message);
    }

    protected function flashWarning(string $message): array
    {
        return $this->flash('warning', $message);
    }

    protected function flashInfo(string $message): array
    {
        return $this->flash('info', $message);
    }
}
