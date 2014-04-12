<?php
namespace DTUXBase\Document\Interfaces;


interface UserInterface {

    public function setChanges($changes);

    public function getChanges();

    public function setCreatedAt($createdAt);

    public function getCreatedAt();

    public function setId($id);

    public function getId();

    public function setNome($nome);

    public function getNome();

    public function setUpdatedAt($updatedAt);

    public function getUpdatedAt();
} 