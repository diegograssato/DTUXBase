<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 03/01/14
 * Time: 23:50
 */

namespace DTUXBase\Document\Interfaces;


interface AdvancedUserInterface {

    /**
     * Verifica se a conta do usuário expirou.
     * @return boolean true se a conta do usuário expirou, false caso contrário não
     */
    public function isAccountNonExpired();

    /**
     * Verifica se o usuário está bloqueado.
     * @return boolean true se o usuário não está bloqueado, false caso contrário
     */
    public function isAccountNonLocked();

    /**
     * Verifica se as credenciais do usuário (senha) expirou.
     * @return boolean true se as credenciais do usuário são expirado, false caso contrário não
     */
    public function isCredentialsNonExpired();

    /**
     * Verifica se o usuário está habilitado.
     * @return boolean true se o usuário estiver ativada, false caso contrário
     */
    public function isEnabled();

} 