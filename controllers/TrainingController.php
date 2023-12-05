<?php

class TrainingController
{
  private $twig;
  private $db;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
  }

  public function index()
  {
    // Obtener información del usuario actual
    $id = $_SESSION['id'];
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener todos los entrenamientos
    $trainings = Training::getAllTrainings($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user[0]['first_name'] . ' ' . $user[0]['last_name'];
    $avatar_fallback = substr($full_name, 0, 2);

    echo $this->twig->render('trainings.twig', [
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'avatar' => $avatar_fallback,
      'trainings' => $trainings
    ]);
  }

  public function view($trainingId)
  {
    $id = $_SESSION['id'];
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener el entrenamiento por id
    $training = Training::getTrainingById($trainingId, $this->db);
    // Obtener los miembros inscritos en el entrenamiento
    $membersInClass = Training::getMembersByTrainingId($trainingId, $this->db);
    // Obtener todos los miembros
    $members = Member::getAllMembers($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user[0]['first_name'] . ' ' . $user[0]['last_name'];
    $avatar_fallback = substr($full_name, 0, 2);

    $memberAddClassMessaje = '';
    if (isset($_SESSION['member_add_class_sucess'])) {
      $memberAddClassMessaje = $_SESSION['member_add_class_sucess'];
      unset($_SESSION['member_add_class_sucess']);
    } else if (isset($_SESSION['member_add_class_error'])) {
      $memberAddClassMessaje = $_SESSION['member_add_class_error'];
      unset($_SESSION['member_add_class_error']);
    }

    $memberRemoveClassMessaje = '';
    if (isset($_SESSION['member_delete_class_sucess'])) {
      $memberRemoveClassMessaje = $_SESSION['member_delete_class_sucess'];
      unset($_SESSION['member_delete_class_sucess']);
    } else if (isset($_SESSION['member_delete_class_error'])) {
      $memberRemoveClassMessaje = $_SESSION['member_delete_class_error'];
      unset($_SESSION['member_delete_class_error']);
    }

    echo $this->twig->render('edit_training.twig', [
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'avatar' => $avatar_fallback,
      'training' => $training[0],
      'members' => $membersInClass,
      'allMembers' => $members,
      'memberAddClassMessaje' => $memberAddClassMessaje,
      'memberRemoveClassMessaje' => $memberRemoveClassMessaje
    ]);
  }

  public function addMemberToTraining($classId)
  {
    // Obtén los parámetros de la solicitud POST
    $memberId = $_POST['member_id'];
    // Prepara la declaración SQL
    $result = Training::addMemberToTraining($memberId, $classId, $this->db);

    if ($result) {
      $_SESSION['member_add_class_sucess'] = 'Miembro añadido a la clase correctamente';
    } else {
      $_SESSION['member_add_class_error'] = 'Error al añadrir el miembro a la clase';
    }

    // Redirige al usuario de vuelta a la página de edición de la clase
    header('Location: /dashboard/training/' . $classId);
  }

  public function deleteMemberFromClass($classId)
  {
    $memberId = $_POST['member_id'];

    $rowCount = Training::deleteMemberFromClass($memberId, $classId, $this->db);

    if ($rowCount > 0) {
      $_SESSION['member_delete_class_sucess'] = 'Miembro eliminado de la clase correctamente';
    } else {
      $_SESSION['member_delete_class_error'] = 'Error al eliminar el miembro de la clase';
    }

    // Redirige al usuario de vuelta a la página de edición de la clase
    header('Location: /dashboard/training/' . $classId);
  }
}
