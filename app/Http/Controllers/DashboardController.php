<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTokenRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Laravel\Sanctum\PersonalAccessToken;

class DashboardController extends Controller
{

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function dashboard(Request $request): View|Factory|Application
    {
        $user = $request->user();

        return view('dashboard', [
            'tokens' => $user->tokens
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function showTokenForm(): View|Factory|Application
    {
        return view('token-create');
    }

    /**
     * @param StoreTokenRequest $request
     * @return Application|Factory|View
     */
    public function createToken(StoreTokenRequest $request): View|Factory|Application
    {
        $request->validated();

        $tokenName = $request->post('name');

        $user = $request->user();
        $token = $user->createToken($tokenName);

        return view('token-show', [
            'tokenName' => $tokenName,
            'token' => $token->plainTextToken
        ]);
    }

    /**
     * @param PersonalAccessToken $token
     * @return Application|RedirectResponse|Redirector
     */
    public function deleteToken(PersonalAccessToken $token): Redirector|RedirectResponse|Application
    {
        $token->delete();
        return redirect('dashboard');
    }
}
