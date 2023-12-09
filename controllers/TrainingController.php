<?php

class TrainingController
{
  private $twig;
  private $db;
  private $session;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
    $this->session = Session::getInstance();
  }

  public function index()
  {
    // Obtener información del usuario actual
    $id = $this->session->get('id');
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener todos los entrenamientos
    $trainings = Training::getAllTrainings($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    // Informacion ultimo incio de sesion
    $last_login_time = $this->session->get('last_login_time');
    $last_login_ip = $this->session->get('last_login_ip');
    $last_login_location = $this->session->get('last_login_location');
    $last_login = $last_login_time . ' desde ' . $last_login_location . ' (' . $last_login_ip . ')';

    $trainingDeleteMessage = $this->session->get('training_delete_sucess') ?? $this->session->get('training_delete_error') ?? '';
    $this->session->remove('training_delete_sucess', 'training_delete_error');

    echo $this->twig->render('trainings.twig', [
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'avatar' => $avatar_fallback,
      'trainings' => $trainings,
      'trainingDeleteMessage' => $trainingDeleteMessage,
      'last_login' => $last_login
    ]);
  }

  public function view($trainingId)
  {
    $id = $this->session->get('id');
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
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    // Informacion ultimo incio de sesion
    $last_login_time = $this->session->get('last_login_time');
    $last_login_ip = $this->session->get('last_login_ip');
    $last_login_location = $this->session->get('last_login_location');
    $last_login = $last_login_time . ' desde ' . $last_login_location . ' (' . $last_login_ip . ')';

    $memberAddClassMessage = $this->session->get('member_add_class_sucess') ?? $this->session->get('member_add_class_error') ?? '';
    $this->session->remove('member_add_class_sucess', 'member_add_class_error');

    $memberRemoveClassMessage = $this->session->get('member_delete_class_sucess') ?? $this->session->get('member_delete_class_error') ?? '';
    $this->session->remove('member_delete_class_sucess', 'member_delete_class_error');

    $trainingUpdateMessage = $this->session->get('training_update_sucess') ?? $this->session->get('training_update_error') ?? '';
    $this->session->remove('training_update_sucess', 'training_update_error');

    echo $this->twig->render('edit-training.twig', [
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'avatar' => $avatar_fallback,
      'training' => $training,
      'members' => $membersInClass,
      'allMembers' => $members,
      'memberAddClassMessage' => $memberAddClassMessage,
      'memberRemoveClassMessage' => $memberRemoveClassMessage,
      'trainingUpdateMessage' => $trainingUpdateMessage,
      'last_login' => $last_login
    ]);
  }

  public function addMemberToTraining($classId)
  {
    // Obtén los parámetros de la solicitud POST
    $memberId = $_POST['member_id'];
    // Prepara la declaración SQL
    $result = Training::addMemberToTraining($memberId, $classId, $this->db);

    if ($result) {
      $this->session->set('member_add_class_sucess', 'Miembro añadido a la clase correctamente');
    } else {
      $this->session->set('member_add_class_error', 'Error al añadrir el miembro a la clase');
    }

    // Redirige al usuario de vuelta a la página de edición de la clase
    header('Location: /dashboard/training/' . $classId);
  }

  public function deleteMemberFromClass($classId)
  {
    $memberId = $_POST['member_id'];

    $rowCount = Training::deleteMemberFromClass($memberId, $classId, $this->db);

    if ($rowCount > 0) {
      $this->session->set('member_delete_class_sucess', 'Miembro eliminado de la clase correctamente');
    } else {
      $this->session->set('member_delete_class_error', 'Error al eliminar el miembro de la clase');
    }

    // Redirige al usuario de vuelta a la página de edición de la clase
    header('Location: /dashboard/training/' . $classId);
  }

  public function create()
  {
    // Obtén los parámetros de la solicitud POST
    $class_name = $_POST['class_name'];
    $class_time = $_POST['class_time'];
    $class_days = $_POST['class_days'];
    $class_duration = $_POST['class_duration'];
    $instructor = $_POST['instructor'];
    $location = $_POST['location'];
    $poster_image = $_POST['poster_image'];

    // Llama a la función createTraining en el modelo Training
    $trainingId = Training::createTraining($class_name, $class_days, $class_time, $class_duration, $instructor, $location, $poster_image, $this->db);

    // Redirige al usuario a la página de visualización del entrenamiento recién creado
    header('Location: /dashboard/training/' . $trainingId);
  }

  public function delete($classId)
  {
    $rowCount = Training::deleteTraining($classId, $this->db);

    if ($rowCount > 0) {
      $this->session->set('training_delete_sucess', 'Entrenamiento eliminado correctamente');
    } else {
      $this->session->set('training_delete_error', 'Error al eliminar el entrenamiento');
    }

    // Redirige al usuario a la página de visualización del entrenamiento recién creado
    header('Location: /dashboard/trainings');
  }

  public function update($trainingId)
  {
    // Obtén los parámetros de la solicitud POST
    $class_name = $_POST['class_name'];
    $class_time = $_POST['class_time'];
    $class_days = $_POST['class_days'];
    $class_duration = $_POST['class_duration'];
    $instructor = $_POST['instructor'];
    $location = $_POST['location'];
    $poster_image = $_POST['poster_image'];

    // Llama a la función updateTraining en el modelo Training
    $rowCount = Training::updateTraining($trainingId, $class_name, $class_days, $class_duration, $class_time,  $instructor, $location, $poster_image, $this->db);

    if ($rowCount > 0) {
      $this->session->set('training_update_sucess', 'Entrenamiento actualizado correctamente');
    } else {
      $this->session->set('training_update_error', 'Error al actualizar el entrenamiento');
    }

    // Redirige al usuario a la página de visualización del entrenamiento actualizado
    header('Location: /dashboard/training/' . $trainingId);
  }
}
