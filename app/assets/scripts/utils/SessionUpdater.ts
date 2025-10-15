export default class SessionUpdater {
  private static readonly SET_ROUTE: string = '/admin/session/set';

  public static update = async (key: string, value: any): Promise<void> => {
    try {
      await fetch(this.SET_ROUTE, {
        method: 'POST',
        body: JSON.stringify({ [key]: value }),
      });
    } catch (error) {
      console.error(error);
    }
  };
}
